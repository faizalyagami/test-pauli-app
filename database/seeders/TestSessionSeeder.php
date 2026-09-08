<?php
// database/seeders/TestSessionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\Applicant;
use App\Models\TestSession;
use App\Models\TestAnswer;
use App\Models\PauliQuestion;

class TestSessionSeeder extends Seeder
{
    public function run()
    {
        // ============================================
        // 1. BUAT SESSION UNTUK TEST STATISTIK (40 * 52) - PAULI-007
        // ============================================
        $statTest = Test::where('test_code', 'PAULI-007')->first();

        if ($statTest) {
            $applicants = Applicant::take(5)->get();

            foreach ($applicants as $index => $applicant) {
                $this->createFullStatisticsSession($applicant, $statTest, $index);
            }
        }

        // ============================================
        // 2. BUAT SESSION UNTUK TEST LAINNYA
        // ============================================
        $tests = Test::where('is_active', true)->get();
        $applicants = Applicant::take(5)->get();

        foreach ($applicants as $applicant) {
            foreach ($tests as $test) {
                // Skip jika test adalah PAULI-007 (sudah dibuat di atas)
                if ($test->test_code === 'PAULI-007') {
                    continue;
                }

                // Skip creating duplicate sessions
                $existing = TestSession::where('applicant_id', $applicant->id)
                    ->where('test_id', $test->id)
                    ->exists();

                if (!$existing && rand(1, 10) > 3) {
                    $this->createCompletedSession($applicant, $test);
                }
            }

            // Create one in-progress session for each applicant
            $randomTest = $tests->random();
            $existingInProgress = TestSession::where('applicant_id', $applicant->id)
                ->where('status', 'in_progress')
                ->exists();

            if (!$existingInProgress && rand(1, 10) > 5) {
                $this->createInProgressSession($applicant, $randomTest);
            }
        }
    }

    /**
     * Create session khusus untuk statistik lengkap (40 * 52)
     * Dengan pola data yang bervariasi untuk grafik kerja
     */
    private function createFullStatisticsSession($applicant, $test, $applicantIndex)
    {
        $startTime = now()->subDays(rand(1, 10))->subMinutes(rand(1, 30));
        $endTime = (clone $startTime)->addMinutes($test->duration_minutes);

        $session = TestSession::create([
            'applicant_id' => $applicant->id,
            'test_id' => $test->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'completed',
            'skipped_columns' => rand(0, 3),
        ]);

        // Generate answers dengan pola berbeda per applicant
        $this->generateStatisticsAnswers($session, $test, $applicantIndex);

        // Calculate and update score
        $session->calculateScore();
    }

    /**
     * Generate answers dengan pola untuk statistik
     * - Membuat kurva naik-turun untuk grafik kerja
     * - 20 interval (setiap 6 menit karena 120 menit / 20 = 6 menit)
     */
    private function generateStatisticsAnswers($session, $test, $applicantIndex)
    {
        $questions = $test->pauliQuestions()->get()->groupBy('column_number');
        $answers = [];

        // ============================================
        // POLA PERFORMA PER INTERVAL (20 interval)
        // Setiap interval = 6 menit (120 menit / 20)
        // ============================================

        // Pola performa berbeda per applicant
        $patterns = [
            0 => [ // Applicant 1: Kurva naik stabil
                1 => 20,
                2 => 22,
                3 => 25,
                4 => 28,
                5 => 30,
                6 => 32,
                7 => 35,
                8 => 38,
                9 => 40,
                10 => 42,
                11 => 44,
                12 => 45,
                13 => 46,
                14 => 48,
                15 => 50,
                16 => 48,
                17 => 45,
                18 => 42,
                19 => 38,
                20 => 35,
            ],
            1 => [ // Applicant 2: Kurva turun naik
                1 => 35,
                2 => 38,
                3 => 40,
                4 => 42,
                5 => 45,
                6 => 40,
                7 => 35,
                8 => 30,
                9 => 25,
                10 => 20,
                11 => 22,
                12 => 25,
                13 => 30,
                14 => 35,
                15 => 40,
                16 => 45,
                17 => 50,
                18 => 48,
                19 => 42,
                20 => 38,
            ],
            2 => [ // Applicant 3: Kurva menurun
                1 => 50,
                2 => 48,
                3 => 45,
                4 => 42,
                5 => 40,
                6 => 38,
                7 => 35,
                8 => 32,
                9 => 30,
                10 => 28,
                11 => 25,
                12 => 22,
                13 => 20,
                14 => 18,
                15 => 15,
                16 => 12,
                17 => 10,
                18 => 8,
                19 => 5,
                20 => 3,
            ],
            3 => [ // Applicant 4: Kurva stabil rendah
                1 => 15,
                2 => 18,
                3 => 20,
                4 => 22,
                5 => 25,
                6 => 28,
                7 => 30,
                8 => 32,
                9 => 35,
                10 => 38,
                11 => 40,
                12 => 42,
                13 => 45,
                14 => 48,
                15 => 50,
                16 => 48,
                17 => 45,
                18 => 42,
                19 => 40,
                20 => 38,
            ],
            4 => [ // Applicant 5: Kurva fluktuatif
                1 => 30,
                2 => 45,
                3 => 25,
                4 => 50,
                5 => 35,
                6 => 48,
                7 => 28,
                8 => 42,
                9 => 32,
                10 => 50,
                11 => 38,
                12 => 45,
                13 => 30,
                14 => 48,
                15 => 35,
                16 => 40,
                17 => 45,
                18 => 30,
                19 => 50,
                20 => 35,
            ],
        ];

        $pattern = $patterns[$applicantIndex % 5] ?? $patterns[0];

        foreach ($questions as $col => $columnQuestions) {
            if ($col <= $session->skipped_columns) continue;

            $intervalIndex = 1;
            $answersInThisInterval = 0;
            $targetPerInterval = $pattern[$intervalIndex] ?? 25;

            foreach ($columnQuestions as $index => $question) {
                if ($index + 1 < count($columnQuestions)) {
                    $nextQuestion = $columnQuestions[$index + 1];
                    $correctValue = ($question->value + $nextQuestion->value) % 10;

                    // Tentukan apakah jawaban benar (75-90% akurasi)
                    $accuracyRate = rand(75, 90);
                    $isCorrect = rand(1, 100) <= $accuracyRate;
                    $answerValue = $isCorrect ? $correctValue : rand(0, 9);

                    // Tentukan line marker (setiap 6 menit = 1 interval)
                    $lineMarker = ceil(($index + 1) / 8) + 1;
                    if ($lineMarker > 20) $lineMarker = 20;

                    $timeTaken = rand(3, 15);

                    $answers[] = [
                        'test_session_id' => $session->id,
                        'question_id' => $question->id,
                        'column_number' => $col,
                        'row_number' => $question->row_number,
                        'answer_value' => $answerValue,
                        'correct_value' => $correctValue,
                        'is_correct' => $isCorrect,
                        'is_revised' => rand(1, 100) <= 15, // 15% revisi
                        'revised_count' => rand(0, 3),
                        'time_taken_seconds' => $timeTaken,
                        'line_marker' => $lineMarker,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $answersInThisInterval++;

                    // Pindah ke interval berikutnya jika sudah mencapai target
                    if ($answersInThisInterval >= $targetPerInterval && $intervalIndex < 20) {
                        $intervalIndex++;
                        $answersInThisInterval = 0;
                        $targetPerInterval = $pattern[$intervalIndex] ?? 25;
                    }
                }
            }
        }

        // Insert answers in chunks
        foreach (array_chunk($answers, 500) as $chunk) {
            TestAnswer::insert($chunk);
        }
    }

    /**
     * Create completed session for regular tests
     */
    private function createCompletedSession($applicant, $test)
    {
        $startTime = now()->subDays(rand(1, 30))->subMinutes(rand(1, 60));
        $endTime = (clone $startTime)->addMinutes($test->duration_minutes);

        $session = TestSession::create([
            'applicant_id' => $applicant->id,
            'test_id' => $test->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'completed',
            'skipped_columns' => rand(0, 3),
        ]);

        // Generate answers based on test type
        $this->generateAnswers($session, $test);

        // Calculate and update score
        $session->calculateScore();
    }

    /**
     * Create in-progress session
     */
    private function createInProgressSession($applicant, $test)
    {
        $startTime = now()->subMinutes(rand(5, $test->duration_minutes - 5));

        $session = TestSession::create([
            'applicant_id' => $applicant->id,
            'test_id' => $test->id,
            'start_time' => $startTime,
            'status' => 'in_progress',
            'skipped_columns' => rand(0, 2),
        ]);

        // Generate partial answers (20-50% completed)
        $this->generatePartialAnswers($session, $test);
    }

    /**
     * Generate full answers for regular test
     */
    private function generateAnswers($session, $test)
    {
        $questions = $test->pauliQuestions()->get()->groupBy('column_number');
        $answers = [];

        // Set accuracy based on test difficulty
        $accuracyRate = $this->getAccuracyRate($test->test_code);

        foreach ($questions as $col => $columnQuestions) {
            $isSkipped = $col <= $session->skipped_columns;
            if ($isSkipped) continue;

            foreach ($columnQuestions as $index => $question) {
                if ($index + 1 < count($columnQuestions)) {
                    $nextQuestion = $columnQuestions[$index + 1];
                    $correctValue = ($question->value + $nextQuestion->value) % 10;

                    // Determine if answer is correct based on accuracy rate
                    $isCorrect = rand(1, 100) <= $accuracyRate;
                    $answerValue = $isCorrect ? $correctValue : rand(0, 9);

                    $answers[] = [
                        'test_session_id' => $session->id,
                        'question_id' => $question->id,
                        'column_number' => $col,
                        'row_number' => $question->row_number,
                        'answer_value' => $answerValue,
                        'correct_value' => $correctValue,
                        'is_correct' => $isCorrect,
                        'is_revised' => rand(1, 100) <= 10,
                        'revised_count' => rand(0, 2),
                        'time_taken_seconds' => rand(2, 12),
                        'line_marker' => floor(($question->row_number - 1) / 2) + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert in chunks
        foreach (array_chunk($answers, 500) as $chunk) {
            TestAnswer::insert($chunk);
        }
    }

    /**
     * Generate partial answers for in-progress test
     */
    private function generatePartialAnswers($session, $test)
    {
        $questions = $test->pauliQuestions()->get()->groupBy('column_number');
        $answers = [];

        // Answer only 20-40% of questions
        $maxRows = rand(2, floor($test->rows_per_column * 0.4));
        $accuracyRate = $this->getAccuracyRate($test->test_code);

        foreach ($questions as $col => $columnQuestions) {
            if ($col <= $session->skipped_columns) continue;

            for ($i = 0; $i < $maxRows && $i < count($columnQuestions) - 1; $i++) {
                $question = $columnQuestions[$i];
                $nextQuestion = $columnQuestions[$i + 1];
                $correctValue = ($question->value + $nextQuestion->value) % 10;

                $isCorrect = rand(1, 100) <= $accuracyRate;
                $answerValue = $isCorrect ? $correctValue : rand(0, 9);

                $answers[] = [
                    'test_session_id' => $session->id,
                    'question_id' => $question->id,
                    'column_number' => $col,
                    'row_number' => $question->row_number,
                    'answer_value' => $answerValue,
                    'correct_value' => $correctValue,
                    'is_correct' => $isCorrect,
                    'is_revised' => rand(1, 100) <= 5,
                    'revised_count' => rand(0, 1),
                    'time_taken_seconds' => rand(2, 8),
                    'line_marker' => floor(($question->row_number - 1) / 2) + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($answers)) {
            TestAnswer::insert($answers);
        }
    }

    /**
     * Get accuracy rate based on test difficulty
     */
    private function getAccuracyRate($testCode)
    {
        switch ($testCode) {
            case 'PAULI-001': // Standard
                return rand(70, 90);
            case 'PAULI-002': // Intensive
                return rand(60, 80);
            case 'PAULI-003': // Quick
                return rand(75, 95);
            case 'PAULI-004': // Advanced
                return rand(65, 85);
            case 'PAULI-005': // Executive
                return rand(55, 75);
            case 'PAULI-006': // Junior
                return rand(80, 98);
            case 'PAULI-007': // Full Statistics
                return rand(70, 85);
            case 'PAULI-008': // Medium Statistics
                return rand(65, 80);
            case 'PAULI-009': // Short Statistics
                return rand(75, 90);
            default:
                return rand(70, 85);
        }
    }
}
