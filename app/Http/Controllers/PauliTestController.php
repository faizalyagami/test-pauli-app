<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\PauliQuestion;
use App\Models\Test;
use App\Models\TestAnswer;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class PauliTestController extends Controller
{

    public function dashboard()
    {
        $stats = [
            'total_applicants' => Applicant::count(),
            'total_tests' => TestSession::count(),
            'completed_tests' => TestSession::where('status', 'completed')->count(),
            'average_score' => TestSession::where('status', 'completed')->avg('score') ?? 0,
        ];

        $recentSessions = TestSession::with(['applicant', 'test'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentApplicants = Applicant::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $testByStatus = TestSession::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Performance by date (last 30 days)
        $performanceByDate = TestSession::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('tester.dashboard', compact(
            'stats',
            'recentSessions',
            'recentApplicants',
            'testByStatus',
            'performanceByDate'
        ));
    }

    public function manageTests()
    {
        $tests = Test::withCount('testSessions')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('tester.tests.index', compact('tests'));
    }

    public function createTest()
    {
        return view('tester.tests.create');
    }

    public function storeTest(Request $request)
    {
        $validated = $request->validate([
            'test_code' => 'required|unique:tests',
            'test_name' => 'required',
            'description' => 'nullable',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'total_columns' => 'required|integer|min:1',
            'rows_per_column' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $test = Test::create($validated);

        return redirect()->route('tester.tests.edit', $test)->with('success', 'Test created successfully');
    }

    public function editTest(Test $test)
    {
        $questions = $test->pauliQuestions()
            ->orderBy('column_number')
            ->orderBy('row_number')
            ->get()
            ->groupBy('column_number');

        return view('tester.tests.edit', compact('test', 'questions'));
    }

    public function updateTest(Request $request, Test $test)
    {
        $validated = $request->validate([
            'test_code' => 'required|unique:tests,test_code,' . $test->id,
            'test_name' => 'required',
            'description' => 'nullable',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'total_columns' => 'required|integer|min:1',
            'rows_per_column' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        $test->update($validated);
        
        return redirect()->route('tester.tests.edit', $test)->with('success', 'Test updated successfully');
    }

    public function destroyTest(Test $test)
    {
        try {
            // Delete related questions first
            $test->pauliQuestions()->delete();
            // Delete test
            $test->delete();
            
            return redirect()->route('tester.tests')->with('success', 'Test deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete test: ' . $e->getMessage());
        }
    }

    public function generateQuestions(Test $test)
    {
        try {
            // Hapus semua soal yang ada
            $test->pauliQuestions()->delete();

            // Buat soal baru
            for ($col = 1; $col <= $test->total_columns; $col++) {
                for ($row = 1; $row <= $test->rows_per_column; $row++) {
                    PauliQuestion::create([
                        'test_id' => $test->id,
                        'column_number' => $col,
                        'row_number' => $row,
                        'value' => rand(0, 9)
                    ]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateQuestion(Request $request, PauliQuestion $question)
    {
        $request->validate([
            'value' => 'required|integer|min:0|max:9',
        ]);

        $question->update(['value' => $request->value]);
        return response()->json(['success' => true]);
    }

    public function testerSettings()
    {
        $user = auth()->user();
        $settings = [
            'email_notifications' => $user->email_notifications ?? true,
            'test_reminders' => $user->test_reminders ?? true,
            'language' => $user->language ?? 'id',
            'timezone' => $user->timezone ?? 'Asia/Jakarta',
            'items_per_page' => $user->items_per_page ?? 20,
            'dashboard_widgets' => $user->dashboard_widgets ?? ['stats', 'recent_tests', 'recent_participants'],
        ];

        return view('tester.settings.index', compact('settings'));
    }

    /**
     * Update Tester Settings
     */
    public function updateTesterSettings(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'test_reminders' => 'boolean',
            'language' => 'in:id,en',
            'timezone' => 'string',
            'items_per_page' => 'integer|min:10|max:100',
            'dashboard_widgets' => 'array',
        ]);

        // Save settings to user meta or separate settings table
        foreach ($validated as $key => $value) {
            $user->{$key} = $value;
        }
        $user->save();

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function sessions(Request $request)
    {
        $query = TestSession::with(['applicant', 'test'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by test
        if ($request->has('test_id') && $request->test_id) {
            $query->where('test_id', $request->test_id);
        }

        // Filter by date
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sessions = $query->paginate(20)->withQueryString();

        $tests = Test::where('is_active', true)->get();

        $stats = [
            'total' => TestSession::count(),
            'in_progress' => TestSession::where('status', 'in_progress')->count(),
            'completed' => TestSession::where('status', 'completed')->count(),
            'evaluated' => TestSession::where('status', 'evaluated')->count(),
        ];

        return view('tester.sessions.index', compact('sessions', 'tests', 'stats'));
    }

    /**
     * Show session detail
     */
    public function showSession(TestSession $session)
    {
        $session->load(['applicant', 'test', 'answers']);

        $scoreData = $session->calculateScore();

        $answersByLine = TestAnswer::where('test_session_id', $session->id)
            ->select('line_marker', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_correct THEN 1 ELSE 0 END) as correct'))
            ->whereNotNull('line_marker')
            ->groupBy('line_marker')
            ->orderBy('line_marker')
            ->get();

        $answersByColumn = TestAnswer::where('test_session_id', $session->id)
            ->select('column_number', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_correct THEN 1 ELSE 0 END) as correct'))
            ->groupBy('column_number')
            ->orderBy('column_number')
            ->get();

        return view('tester.sessions.show', compact('session', 'scoreData', 'answersByLine', 'answersByColumn'));
    }

    /**
     * Monitor live test session
     */
    public function monitorSession(TestSession $session)
    {
        if ($session->status !== 'in_progress') {
            return redirect()->route('tester.sessions.show', $session)
                ->with('warning', 'Session is not in progress');
        }

        $session->load(['applicant', 'test']);

        return view('tester.sessions.monitor', compact('session'));
    }

    /**
     * Cancel a test session
     */
    public function cancelSession(TestSession $session)
    {
        if ($session->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot cancel completed session');
        }

        $session->update([
            'status' => 'cancelled',
            'end_time' => now(),
        ]);

        return redirect()->route('tester.sessions')
            ->with('success', 'Session cancelled successfully');
    }

    /**
     * Get active sessions (for AJAX)
     */
    public function activeSessions()
    {
        $sessions = TestSession::with(['applicant', 'test'])
            ->where('status', 'in_progress')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($sessions);
    }

    /**
     * Get completed sessions (for AJAX)
     */
    public function completedSessions()
    {
        $sessions = TestSession::with(['applicant', 'test'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if (request()->wantsJson()) {
            return response()->json($sessions);
        }

        return view('tester.sessions.completed', compact('sessions'));
    }

    public function manageApplicants(Request $request)
    {
        $query = Applicant::with('user');

        // Apply filters
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('participant_numb', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('gender') && $request->gender) {
            $query->where('gender', $request->gender);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('registration_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('registration_date', '<=', $request->date_to);
        }

        $applicants = $query->orderBy('registration_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Stats for cards
        $totalApplicants = Applicant::count();
        $testTakenCount = Applicant::where('status', 'test_taken')->count();
        $acceptedCount = Applicant::where('status', 'accepted')->count();
        $registeredToday = Applicant::whereDate('registration_date', today())->count();

        return view('tester.applicants.index', compact(
            'applicants',
            'totalApplicants',
            'testTakenCount',
            'acceptedCount',
            'registeredToday'
        ));
    }

    public function createApplicant()
    {
        return view('tester.applicants.create');
    }

    public function storeApplicant(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'nullable',
            'phone' => 'required',
            'education_background' => 'nullable',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        DB::beginTransaction();
        try {
            // Create user
            $user = \App\Models\User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => 'applicant',
                'is_active' => true,
            ]);

            // Generate participant number
            $lastParticipant = Applicant::latest('id')->first();
            $participant_numb = 'P' . str_pad(($lastParticipant ? $lastParticipant->id + 1 : 1), 6, '0', STR_PAD_LEFT);

            // Create applicant
            $applicant = Applicant::create([
                'user_id' => $user->id,
                'participant_numb' => $participant_numb,
                'full_name' => $validated['full_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'education_background' => $validated['education_background'],
                'registration_date' => now(),
                'status' => 'registered',
            ]);

            DB::commit();
            return redirect()->route('tester.applicants.show', $applicant)->with('success', 'Applicant created');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create applicant: ' . $e->getMessage());
        }
    }

    public function editApplicant(Applicant $applicant)
    {
        return view('tester.applicants.edit', compact('applicant'));
    }

    public function updateApplicant(Request $request, Applicant $applicant)
    {
        $validated = $request->validate([
            'full_name' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'nullable',
            'phone' => 'required',
            'education_background' => 'nullable',
        ]);

        $applicant->update($validated);

        // Update user name
        $applicant->user->update([
            'name' => $validated['full_name']
        ]);

        return redirect()->route('tester.applicants.show', $applicant)
            ->with('success', 'Applicant updated successfully');
    }

    public function showApplicant(Applicant $applicant)
    {
        // Load test sessions with test relationship
        $testSessions = $applicant->testSessions()
            ->with('test')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_tests' => $testSessions->count(),
            'completed_tests' => $testSessions->where('status', 'completed')->count(),
            'average_score' => $testSessions->where('status', 'completed')->avg('score') ?? 0,
            'best_score' => $testSessions->where('status', 'completed')->max('score') ?? 0,
        ];

        // Perbaiki: gunakan $applicant (single) dan $testSessions
        return view('tester.applicants.show', compact('applicant', 'testSessions', 'stats'));
    }

    public function destroyApplicant(Applicant $applicant)
    {
        try {
            // Delete user (cascade will delete applicant)
            $applicant->user->delete();

            return redirect()->route('tester.applicants')
                ->with('success', 'Applicant deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete applicant: ' . $e->getMessage());
        }
    }

    public function startTest(Applicant $applicant, Test $test)
    {
        //Check if there's existing session
        $session = TestSession::where('applicant_id', $applicant->id)
            ->where('test_id', $test->id)
            ->whereIn('status', ['scheduled', 'inprogress'])
            ->first();
        if (!$session) {
            $session = TestSession::create([
                'applicant_id' => $applicant->id,
                'test_id' => $test->id,
                'status' => 'in_progress',
                'start_time' => now()
            ]);
            $applicant->update(['status' => 'test_taken']);
        }
        $testData = $test->generatePauliGrid();

        return view('pauli-test.index', compact('test', 'applicant', 'session', 'testData'));
    }

    public function saveAnswer(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:test_sessions_id',
            'column' => 'required|integer',
            'row' => 'required|integer',
            'answer' => 'required|string|max:1',
            'time_taken' => 'nullable|integer',
            'line_marker' => 'nullable|integer',
        ]);

        $session = TestSession::findOrFail($validated['session_id']);

        // Get question in single query
        $questions = PauliQuestion::where('test_id', $session->test_id)
            ->where('column_number', $validated['column'])
            ->whereIn('row_number', [$validated['row'], $validated['row'] + 1])
            ->get()
            ->keyBy('row_number');

        $currentQuestion = $questions->get($validated['row']);
        $nextQuestion = $questions->get($validated['row'] + 1);

        if ($currentQuestion && $nextQuestion) {
            $correctValue = ($currentQuestion->value + $nextQuestion->value) % 10;
            $isCorrect = (int)$validated['answer'] === $correctValue;
        } else {
            $correctValue = null;
            $isCorrect = false;
        }

        TestAnswer::updateOrCreate(
            [
                'test_session_id' => $validated['session_id'],
                'column_number' => $validated['column'],
                'row_number' => $validated['row'],
            ],
            [
                'question_id' => $currentQuestion ? $currentQuestion->id : null,
                'answer_value' => $validated['answer'],
                'correct_value' => $correctValue,
                'is_correct' => $isCorrect,
                'time_taken_seconds' => $validated['time_taken'],
                'line_marker' => $validated['line_marker'],
            ]
        );
        return response()->json(['success' => true]);
    }
    public function batchSaveAnswers(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:test_sessions,id',
            'answers' => 'required|array',
            'answers.*.column' => 'required|integer',
            'answers.*.row' => 'required|integer',
            'answers.*.answer' => 'required|string|max:1',
        ]);

        $session = TestSession::findOrFail($validated['session_id']);

        $allQuestions = PauliQuestion::where('test_id', $session->id)
            ->get()
            ->groupBy(function ($q) {
                return $q->column_number . '_' . $q->row_number;
            });

        $answers = [];
        foreach ($validated['answers'] as $answerData) {
            $key = $answerData['column'] . '_' . $answerData['row'];
            $currentQuestion = $allQuestions->get($key);
            $nextKey = $answerData['column'] . '_' . ($answerData['row'] + 1);
            $nextQuestion = $allQuestions->get($nextKey);

            if ($currentQuestion && $nextQuestion) {
                $correctValue = ($currentQuestion->value + $nextQuestion->value) % 10;
                $isCorrect = (int)$answerData['answer'] === $correctValue;
            } else {
                $correctValue = null;
                $isCorrect = false;
            }

            $answers[] = [
                'test_session_id' => $validated['session_id'],
                'question_id' => $currentQuestion ? $currentQuestion->id : null,
                'column_number' => $answerData['column'],
                'row_number' => $answerData['row'],
                'answer_value' => $answerData['answer'],
                'correct_value' => $correctValue,
                'is_correct' => $isCorrect,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        TestAnswer::upsert($answers, ['test_session_id', 'column_number', 'row_number'], ['answer_value', 'correct_value', 'is_correct', 'updated_at']);
        return response()->json(['success' => true, 'count' => count($answers)]);
    }

    public function result($sessionId)
    {
        // Load session with eager loading
        $session = TestSession::with([
            'applicant',
            'test',
            'answers' => function ($query) {
                $query->select('id', 'test_session_id', 'line_marker', 'column_number', 'is_correct');
            }
        ])->findOrFail($sessionId);

        // Calculate score data
        $scoreData = $session->calculateScore();

        // Optimize answers by line with single query
        $answersByLine = TestAnswer::where('test_session_id', $sessionId)
            ->select('line_marker', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_correct THEN 1 ELSE 0 END) as correct'))
            ->whereNotNull('line_marker')
            ->groupBy('line_marker')
            ->orderBy('line_marker')
            ->get();

        // Optimize answers by column with single query
        $answersByColumn = TestAnswer::where('test_session_id', $sessionId)
            ->select('column_number', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_correct THEN 1 ELSE 0 END) as correct'))
            ->groupBy('column_number')
            ->orderBy('column_number')
            ->get();

        // Optimize time series with single query
        $timeSeries = TestAnswer::where('test_session_id', $sessionId)
            ->select(DB::raw('FLOOR(time_taken_seconds / 60) as minute'), DB::raw('COUNT(*) as answers'), DB::raw('SUM(CASE WHEN is_correct THEN 1 ELSE 0 END) as correct'))
            ->whereNotNull('time_taken_seconds')
            ->groupBy('minute')
            ->orderBy('minute')
            ->get();

        // Get performance by column for heatmap
        $performanceMatrix = TestAnswer::where('test_session_id', $sessionId)
            ->select('column_number', 'row_number', 'is_correct')
            ->orderBy('column_number')
            ->orderBy('row_number')
            ->get()
            ->groupBy('column_number');

        return view('pauli-test.result', compact(
            'session',
            'scoreData',
            'answersByLine',
            'answersByColumn',
            'timeSeries',
            'performanceMatrix'
        ));
    }

    public function exportResults(Request $request)
    {
        $sessionIds = $request->session_ids;

        return response()->stream(function () use ($sessionIds) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No. Peserta', 'Nama', 'Skor', 'Akurasi', 'Tanggal Tes']);

            // Use chunk to avoid memory issues
            TestSession::with(['applicant'])
                ->whereIn('id', $sessionIds)
                ->chunk(100, function ($sessions) use ($handle) {
                    foreach ($sessions as $session) {
                        $scoreData = $session->calculateScore();
                        fputcsv($handle, [
                            $session->applicant->participant_numb,
                            $session->applicant->full_name,
                            $scoreData['correct'],
                            number_format($scoreData['accuracy'], 2) . '%',
                            $session->created_at->format('d/m/Y H:i')
                        ]);
                    }
                });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="test_results.csv"',
        ]);
    }

    public function reports(Request $request)
    {
        $query = TestSession::with(['applicant', 'test'])
            ->where('status', 'completed');

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by test
        if ($request->has('test_id') && $request->test_id) {
            $query->where('test_id', $request->test_id);
        }

        // Filter by applicant
        if ($request->has('applicant_id') && $request->applicant_id) {
            $query->where('applicant_id', $request->applicant_id);
        }

        // Filter by score range
        if ($request->has('min_score') && $request->min_score) {
            $query->where('score', '>=', $request->min_score);
        }

        if ($request->has('max_score') && $request->max_score) {
            $query->where('score', '<=', $request->max_score);
        }

        $sessions = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Summary statistics
        $summary = [
            'total_sessions' => $query->count(),
            'average_score' => $query->avg('score') ?? 0,
            'highest_score' => $query->max('score') ?? 0,
            'lowest_score' => $query->min('score') ?? 0,
            'total_answers' => TestAnswer::whereIn('test_session_id', $query->pluck('id'))->count(),
            'total_correct' => TestAnswer::whereIn('test_session_id', $query->pluck('id'))->where('is_correct', true)->count(),
        ];

        $summary['accuracy'] = $summary['total_answers'] > 0
            ? ($summary['total_correct'] / $summary['total_answers']) * 100
            : 0;

        // Data for charts
        $tests = Test::where('is_active', true)->get();
        $applicants = Applicant::orderBy('full_name')->get();

        // Performance by test
        $performanceByTest = TestSession::where('status', 'completed')
            ->select('test_id', DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as total'))
            ->groupBy('test_id')
            ->with('test')
            ->get();

        // Performance by date (last 30 days)
        $performanceByDate = TestSession::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('tester.reports.index', compact(
            'sessions',
            'tests',
            'applicants',
            'summary',
            'performanceByTest',
            'performanceByDate',
            'request'
        ));
    }

    /**
     * Export Excel Report
     */
    public function exportExcel(Request $request)
    {
        $query = TestSession::with(['applicant', 'test'])
            ->where('status', 'completed');

        // Apply same filters as reports
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->has('test_id') && $request->test_id) {
            $query->where('test_id', $request->test_id);
        }

        $sessions = $query->orderBy('created_at', 'desc')->get();

        // Generate Excel file
        $filename = 'test_report_' . now()->format('Ymd_His') . '.xlsx';

        // You can use Maatwebsite Excel package here
        // return Excel::download(new TestReportExport($sessions), $filename);

        // Simple CSV export
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($sessions) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'No',
                'Date',
                'Participant Number',
                'Participant Name',
                'Test Name',
                'Score',
                'Total Answers',
                'Correct Answers',
                'Accuracy',
                'Time Spent (minutes)'
            ]);

            // Add data
            foreach ($sessions as $index => $session) {
                $totalAnswers = $session->answers()->count();
                $correctAnswers = $session->answers()->where('is_correct', true)->count();
                $accuracy = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0;

                fputcsv($file, [
                    $index + 1,
                    $session->created_at->format('d/m/Y H:i'),
                    $session->applicant->participant_numb,
                    $session->applicant->full_name,
                    $session->test->test_name,
                    $session->score ?? 0,
                    $totalAnswers,
                    $correctAnswers,
                    number_format($accuracy, 2) . '%',
                    $session->start_time && $session->end_time ? $session->start_time->diffInMinutes($session->end_time) : 0
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export PDF Report
     */
    public function exportPdf(Request $request)
    {
        $query = TestSession::with(['applicant', 'test'])
            ->where('status', 'completed');

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->has('test_id') && $request->test_id) {
            $query->where('test_id', $request->test_id);
        }

        $sessions = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total_sessions' => $sessions->count(),
            'average_score' => $sessions->avg('score') ?? 0,
            'total_participants' => $sessions->pluck('applicant_id')->unique()->count(),
        ];

        // Generate PDF using DomPDF
        $pdf = PDF::loadView('tester.reports.pdf', compact('sessions', 'summary', 'request'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('test_report_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Settings Users Page
     */
    public function settingsUsers()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('tester.settings.users', compact('users'));
    }

    /**
     * Get User for Edit
     */
    public function getUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
        ]);
    }

    /**
     * Store New User
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,tester,applicant',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
        ]);

        // If role is applicant, create applicant profile
        if ($user->role === 'applicant') {
            $participant_numb = Applicant::generateParticipantNumber();
            Applicant::create([
                'user_id' => $user->id,
                'participant_numb' => $participant_numb,
                'full_name' => $validated['name'],
                'date_of_birth' => now()->subYears(20),
                'gender' => 'male',
                'address' => '',
                'phone' => '',
                'registration_date' => now(),
                'status' => 'registered',
            ]);
        }

        return redirect()->route('tester.settings.users')->with('success', 'User created successfully');
    }

    /**
     * Update User
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,tester,applicant',
            'is_active' => 'boolean',
        ]);

        $oldRole = $user->role;

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Handle applicant profile if role changed
        if ($oldRole !== $user->role) {
            if ($user->role === 'applicant' && !$user->applicant) {
                $participant_numb = Applicant::generateParticipantNumber();
                Applicant::create([
                    'user_id' => $user->id,
                    'participant_numb' => $participant_numb,
                    'full_name' => $user->name,
                    'date_of_birth' => now()->subYears(20),
                    'gender' => 'male',
                    'address' => '',
                    'phone' => '',
                    'registration_date' => now(),
                    'status' => 'registered',
                ]);
            } elseif ($oldRole === 'applicant' && $user->applicant) {
                $user->applicant->delete();
            }
        }

        return redirect()->route('tester.settings.users')->with('success', 'User updated successfully');
    }

    /**
     * Toggle User Status
     */
    public function toggleUserStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $newStatus = $request->status === 'active';
        $user->update(['is_active' => $newStatus]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete User
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('tester.settings.users')->with('success', 'User deleted successfully');
    }

    /**
     * Settings Logs Page
     */
    public function settingsLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logFile)) {
            $content = File::get($logFile);
            $logLines = explode("\n", $content);
            $logs = array_reverse(array_filter($logLines));
            $logs = array_slice($logs, 0, 500);
        }

        return view('tester.settings.logs', compact('logs'));
    }

    /**
     * Clear Logs
     */
    public function clearLogs()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            if (File::exists($logFile)) {
                File::put($logFile, '');
            }
            return response()->json(['success' => true, 'message' => 'Logs cleared successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to clear logs']);
        }
    }

    /**
     * Create Backup
     */
    public function createBackup()
    {
        try {
            Artisan::call('backup:run');
            return response()->json(['success' => true, 'message' => 'Backup created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create backup: ' . $e->getMessage()]);
        }
    }

    /**
     * Clear Cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json(['success' => true, 'message' => 'Cache cleared successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to clear cache']);
        }
    }
}
