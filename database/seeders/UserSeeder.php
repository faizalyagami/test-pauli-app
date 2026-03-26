<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Applicant;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@pauli.com'],
            [
                'name' => 'Administrator',
                'email' => 'admin@pauli.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Create Tester Users
        $testers = [
            ['name' => 'Tester Utama', 'email' => 'tester@pauli.com'],
            ['name' => 'Tester Psikologi', 'email' => 'psikolog@pauli.com'],
            ['name' => 'Tester HRD', 'email' => 'hrd@pauli.com'],
        ];

        foreach ($testers as $testerData) {
            User::updateOrCreate(
                ['email' => $testerData['email']],
                [
                    'name' => $testerData['name'],
                    'email' => $testerData['email'],
                    'password' => Hash::make('password'),
                    'role' => 'tester',
                    'is_active' => true,
                ]
            );
        }

        // Create Applicant Users
        $applicants = [
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'phone' => '081234567891', 'gender' => 'male', 'education' => 'S1 Teknik Informatika', 'institution' => 'Universitas Indonesia'],
            ['name' => 'Siti Aminah', 'email' => 'siti@example.com', 'phone' => '081234567892', 'gender' => 'female', 'education' => 'S1 Psikologi', 'institution' => 'Universitas Gadjah Mada'],
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@example.com', 'phone' => '081234567893', 'gender' => 'male', 'education' => 'S1 Manajemen', 'institution' => 'Universitas Padjadjaran'],
            ['name' => 'Dewi Kartika', 'email' => 'dewi@example.com', 'phone' => '081234567894', 'gender' => 'female', 'education' => 'S1 Hukum', 'institution' => 'Universitas Airlangga'],
            ['name' => 'Rizky Ramadhan', 'email' => 'rizky@example.com', 'phone' => '081234567895', 'gender' => 'male', 'education' => 'S1 Sistem Informasi', 'institution' => 'Institut Teknologi Bandung'],
            ['name' => 'Nurul Hidayah', 'email' => 'nurul@example.com', 'phone' => '081234567896', 'gender' => 'female', 'education' => 'S1 Komunikasi', 'institution' => 'Universitas Hasanuddin'],
            ['name' => 'Andi Wijaya', 'email' => 'andi@example.com', 'phone' => '081234567897', 'gender' => 'male', 'education' => 'S1 Teknik Elektro', 'institution' => 'Universitas Brawijaya'],
            ['name' => 'Ratna Sari', 'email' => 'ratna@example.com', 'phone' => '081234567898', 'gender' => 'female', 'education' => 'S1 Akuntansi', 'institution' => 'Universitas Diponegoro'],
        ];

        foreach ($applicants as $index => $applicantData) {
            $user = User::updateOrCreate(
                ['email' => $applicantData['email']],
                [
                    'name' => $applicantData['name'],
                    'email' => $applicantData['email'],
                    'password' => Hash::make('password'),
                    'role' => 'applicant',
                    'is_active' => true,
                ]
            );

            // Generate participant number
            $participant_numb = 'P' . str_pad(($index + 1), 6, '0', STR_PAD_LEFT);

            Applicant::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'participant_numb' => $participant_numb,
                    'full_name' => $applicantData['name'],
                    'date_of_birth' => '1995-01-' . str_pad(($index + 1), 2, '0', STR_PAD_LEFT),
                    'gender' => $applicantData['gender'],
                    'address' => 'Jl. Contoh No. ' . ($index + 1) . ', Jakarta',
                    'phone' => $applicantData['phone'],
                    'education_background' => $applicantData['education'],
                    'institution' => $applicantData['institution'],
                    'registration_date' => now(),
                    'status' => 'registered',
                ]
            );
        }
    }
}
