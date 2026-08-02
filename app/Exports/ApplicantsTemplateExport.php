<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApplicantsTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            [
                'John Doe',
                'john.doe@example.com',
                '08123456789',
                '1990-01-15',
                'male',
                'Jl. Contoh No. 123 Jakarta',
                'S1 Teknik Informatika',
                'Universitas Indonesia'
            ],
            [
                'Jane Smith',
                'jane.smith@example.com',
                '08198765432',
                '1995-05-20',
                'female',
                'Jl. Contoh No. 456 Bandung',
                'S2 Manajemen',
                'Universitas Padjadjaran'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'full_name',
            'email',
            'phone',
            'date_of_birth',
            'gender',
            'address',
            'education_background',
            'institution'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
