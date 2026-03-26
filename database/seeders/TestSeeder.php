<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\PauliQuestion;

class TestSeeder extends Seeder
{
    public function run()
    {
        // Create Pauli Test - Standard
        $standardTest = Test::updateOrCreate(
            ['test_code' => 'PAULI-001'],
            [
                'test_name' => 'Pauli Test - Standard',
                'description' => 'Tes Pauli standar dengan 300 soal dalam 30 menit. Tes ini mengukur kemampuan konsentrasi, ketelitian, dan daya tahan kerja. Cocok untuk seleksi umum.',
                'duration_minutes' => 30,
                'total_questions' => 300,
                'total_columns' => 50,
                'rows_per_column' => 6,
                'is_active' => true,
            ]
        );
        $this->generatePauliQuestions($standardTest);

        // Create Pauli Test - Intensive
        $intensiveTest = Test::updateOrCreate(
            ['test_code' => 'PAULI-002'],
            [
                'test_name' => 'Pauli Test - Intensive',
                'description' => 'Tes Pauli intensif dengan 500 soal dalam 45 menit. Tes ini mengukur ketahanan mental dan kecepatan kerja di bawah tekanan. Cocok untuk posisi dengan tuntutan tinggi.',
                'duration_minutes' => 45,
                'total_questions' => 500,
                'total_columns' => 50,
                'rows_per_column' => 10,
                'is_active' => true,
            ]
        );
        $this->generatePauliQuestions($intensiveTest);

        // Create Pauli Test - Quick Assessment
        $quickTest = Test::updateOrCreate(
            ['test_code' => 'PAULI-003'],
            [
                'test_name' => 'Pauli Test - Quick Assessment',
                'description' => 'Tes Pauli cepat dengan 150 soal dalam 15 menit. Tes ini memberikan gambaran awal tentang kemampuan konsentrasi peserta. Cocok untuk screening awal.',
                'duration_minutes' => 15,
                'total_questions' => 150,
                'total_columns' => 25,
                'rows_per_column' => 6,
                'is_active' => true,
            ]
        );
        $this->generatePauliQuestions($quickTest);

        // Create Pauli Test - Advanced
        $advancedTest = Test::updateOrCreate(
            ['test_code' => 'PAULI-004'],
            [
                'test_name' => 'Pauli Test - Advanced',
                'description' => 'Tes Pauli advanced dengan 400 soal dalam 35 menit. Tes ini dirancang untuk posisi yang membutuhkan konsentrasi tinggi dan ketelitian ekstra.',
                'duration_minutes' => 35,
                'total_questions' => 400,
                'total_columns' => 40,
                'rows_per_column' => 10,
                'is_active' => true,
            ]
        );
        $this->generatePauliQuestions($advancedTest);

        // Create Pauli Test - Executive
        $executiveTest = Test::updateOrCreate(
            ['test_code' => 'PAULI-005'],
            [
                'test_name' => 'Pauli Test - Executive',
                'description' => 'Tes Pauli khusus untuk posisi eksekutif dengan 600 soal dalam 60 menit. Tes ini mengukur kemampuan multitasking dan daya tahan mental tingkat tinggi.',
                'duration_minutes' => 60,
                'total_questions' => 600,
                'total_columns' => 60,
                'rows_per_column' => 10,
                'is_active' => false,
            ]
        );
        $this->generatePauliQuestions($executiveTest);

        // Create Pauli Test - Junior
        $juniorTest = Test::updateOrCreate(
            ['test_code' => 'PAULI-006'],
            [
                'test_name' => 'Pauli Test - Junior',
                'description' => 'Tes Pauli untuk level junior dengan 200 soal dalam 20 menit. Tes ini menggunakan angka yang lebih sederhana dan waktu yang lebih singkat.',
                'duration_minutes' => 20,
                'total_questions' => 200,
                'total_columns' => 40,
                'rows_per_column' => 5,
                'is_active' => true,
            ]
        );
        $this->generatePauliQuestions($juniorTest);
    }

    private function generatePauliQuestions($test)
    {
        // Delete existing questions
        $test->pauliQuestions()->delete();

        $records = [];

        // Generate numbers based on test difficulty
        for ($col = 1; $col <= $test->total_columns; $col++) {
            for ($row = 1; $row <= $test->rows_per_column; $row++) {
                $value = $this->getNumberPattern($test->test_code, $col, $row);

                $records[] = [
                    'test_id' => $test->id,
                    'column_number' => $col,
                    'row_number' => $row,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($records, 500) as $chunk) {
            PauliQuestion::insert($chunk);
        }
    }

    private function getNumberPattern($testCode, $col, $row)
    {
        switch ($testCode) {
            case 'PAULI-001': // Standard - Random
                return rand(0, 9);

            case 'PAULI-002': // Intensive - More challenging numbers
                $value = rand(0, 9);
                if (rand(1, 10) > 7) {
                    $value = rand(5, 9);
                }
                return $value;

            case 'PAULI-003': // Quick - Simpler numbers
                if (rand(1, 10) > 6) {
                    return rand(0, 4);
                }
                return rand(0, 9);

            case 'PAULI-004': // Advanced - More complex
                $value = rand(0, 9);
                if (rand(1, 10) > 5) {
                    $value = rand(5, 9);
                }
                return $value;

            case 'PAULI-005': // Executive - Highest complexity
                $value = rand(0, 9);
                if (rand(1, 10) > 4) {
                    $value = rand(6, 9);
                }
                return $value;

            case 'PAULI-006': // Junior - Very simple
                if (rand(1, 10) > 3) {
                    return rand(0, 5);
                }
                return rand(0, 9);

            default:
                return rand(0, 9);
        }
    }
}
