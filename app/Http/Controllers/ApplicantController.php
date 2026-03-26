<?php
// app/Http/Controllers/ApplicantController.php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Test;
use App\Models\TestSession;
use App\Models\TestAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicantController extends Controller
{
    /**
     * Applicant Dashboard
     */
    public function dashboard()
    {
        $applicant = Auth::user()->applicant;

        $testSessions = TestSession::where('applicant_id', $applicant->id)
            ->with('test')
            ->orderBy('created_at', 'desc')
            ->get();

        $availableTests = Test::where('is_active', true)
            ->whereDoesntHave('testSessions', function ($query) use ($applicant) {
                $query->where('applicant_id', $applicant->id);
            })
            ->get();

        $stats = [
            'total_tests' => $testSessions->count(),
            'completed_tests' => $testSessions->where('status', 'completed')->count(),
            'average_score' => $testSessions->where('status', 'completed')->avg('score') ?? 0,
            'best_score' => $testSessions->where('status', 'completed')->max('score') ?? 0,
        ];

        // Recent activities - 5 most recent test sessions
        $recentActivities = $testSessions->take(5);

        return view('applicant.dashboard', compact('applicant', 'testSessions', 'availableTests', 'stats', 'recentActivities'));
    }

    /**
     * Available Tests
     */
    public function availableTests()
    {
        $applicant = Auth::user()->applicant;

        $tests = Test::where('is_active', true)
            ->whereDoesntHave('testSessions', function ($query) use ($applicant) {
                $query->where('applicant_id', $applicant->id);
            })
            ->get();

        return view('applicant.tests', compact('applicant', 'tests'));
    }

    /**
     * Start Test
     */
    public function startTest(Test $test)
    {
        $applicant = Auth::user()->applicant;

        // Check if test is active
        if (!$test->is_active) {
            return redirect()->route('applicant.tests')->with('error', 'Test is not active.');
        }

        // Check if already taken
        $existingSession = TestSession::where('applicant_id', $applicant->id)
            ->where('test_id', $test->id)
            ->whereIn('status', ['in_progress', 'completed'])
            ->first();

        if ($existingSession && $existingSession->status == 'completed') {
            return redirect()->route('applicant.history')->with('warning', 'You have already completed this test.');
        }

        if ($existingSession && $existingSession->status == 'in_progress') {
            return redirect()->route('pauli-test.start', [$applicant, $test]);
        }

        return redirect()->route('pauli-test.start', [$applicant, $test]);
    }

    /**
     * Applicant Profile
     */
    public function profile()
    {
        $applicant = Auth::user()->applicant;
        $user = Auth::user();

        return view('applicant.profile', compact('applicant', 'user'));
    }

    /**
     * Update Profile
     */
    public function updateProfile(Request $request)
    {
        $applicant = Auth::user()->applicant;
        $user = Auth::user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string',
            'education_background' => 'nullable|string',
            'institution' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
        ]);

        DB::beginTransaction();
        try {
            // Update user name
            $user->update([
                'name' => $validated['full_name'],
            ]);

            // Update applicant profile
            $applicant->update([
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'education_background' => $validated['education_background'],
                'institution' => $validated['institution'],
                'date_of_birth' => $validated['date_of_birth'] ?? $applicant->date_of_birth,
                'gender' => $validated['gender'] ?? $applicant->gender,
            ]);

            DB::commit();
            return redirect()->route('applicant.profile')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Test History
     */
    public function history(Request $request)
    {
        $applicant = Auth::user()->applicant;

        $query = TestSession::where('applicant_id', $applicant->id)
            ->with('test');

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $testSessions = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => $testSessions->total(),
            'completed' => TestSession::where('applicant_id', $applicant->id)->where('status', 'completed')->count(),
            'average_score' => TestSession::where('applicant_id', $applicant->id)->where('status', 'completed')->avg('score') ?? 0,
        ];

        return view('applicant.history', compact('applicant', 'testSessions', 'summary'));
    }

    /**
     * Show Test Result
     */
    public function showResult(TestSession $session)
    {
        // Check ownership
        if ($session->applicant_id != Auth::user()->applicant->id) {
            abort(403, 'Unauthorized');
        }

        $scoreData = $session->calculateScore();

        $answersByLine = TestAnswer::where('test_session_id', $session->id)
            ->select('line_marker', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_correct THEN 1 ELSE 0 END) as correct'))
            ->whereNotNull('line_marker')
            ->groupBy('line_marker')
            ->orderBy('line_marker')
            ->get();

        return view('applicant.result', compact('session', 'scoreData', 'answersByLine'));
    }

    /**
     * Statistics
     */
    public function statistics()
    {
        $applicant = Auth::user()->applicant;

        // Get test sessions
        $testSessions = TestSession::where('applicant_id', $applicant->id)->get();
        $completedSessions = $testSessions->where('status', 'completed');

        // Calculate answer statistics
        $totalAnswers = 0;
        $correctAnswers = 0;

        foreach ($completedSessions as $session) {
            $totalAnswers += $session->answers()->count();
            $correctAnswers += $session->answers()->where('is_correct', true)->count();
        }

        $stats = [
            'total_tests' => $testSessions->count(),
            'completed_tests' => $completedSessions->count(),
            'average_score' => $completedSessions->avg('score') ?? 0,
            'best_score' => $completedSessions->max('score') ?? 0,
            'total_answers' => $totalAnswers,
            'correct_answers' => $correctAnswers,
        ];

        // Performance over time
        $performanceOverTime = TestSession::where('applicant_id', $applicant->id)
            ->where('status', 'completed')
            ->orderBy('created_at')
            ->get(['created_at', 'score'])
            ->map(function ($session) {
                return [
                    'date' => $session->created_at->format('d M Y'),
                    'score' => $session->score,
                ];
            });

        return view('applicant.statistics', compact('applicant', 'stats', 'performanceOverTime'));
    }

    public static function generateParticipantNumber()
    {
        $last = self::latest('id')->first();
        $number = $last ? intval(substr($last->participant_numb, 1)) + 1 : 1;
        return 'P' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
