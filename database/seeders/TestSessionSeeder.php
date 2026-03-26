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
        $tests = Test::where('is_active', true)->get();
        $applicants = Applicant::take(5)->get(); // Only create sessions for first 5 applicants

        foreach ($applicants as $applicant) {
            foreach ($tests as $test) {
                // Skip creating duplicate sessions
                $existing = TestSession::where('applicant_id', $applicant->id)
                    ->where('test_id', $test->id)
                    ->exists();

                if (!$existing && rand(1, 10) > 3) { // 70% chance to create session
                    $this->createCompletedSession($applicant, $test);
                }
            }

            // Create one in-progress session for each applicant
            $randomTest = $tests->random();
            $existingInProgress = TestSession::where('applicant_id', $applicant->id)
                ->where('status', 'in_progress')
                ->exists();

            if (!$existingInProgress && rand(1, 10) > 5) { // 50% chance
                $this->createInProgressSession($applicant, $randomTest);
            }
        }
    }

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

    private function getAccuracyRate($testCode)
    {
        // Different accuracy rates based on test difficulty
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
            default:
                return rand(70, 85);
        }
    }
}
