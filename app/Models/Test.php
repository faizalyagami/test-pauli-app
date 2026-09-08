<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_code',
        'test_name',
        'description',
        'duration_minutes',
        'total_questions',
        'total_columns',
        'rows_per_column',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pauliQuestions()
    {
        return $this->hasMany(PauliQuestion::class);
    }

    public function testSessions()
    {
        return $this->hasMany(TestSession::class);
    }

    public function generatePauliGrid()
    {
        $questions = $this->pauliQuestions()
            ->orderBy('column_number')
            ->orderBy('row_number')
            ->get();
        
        $grid = [];
        
        // Inisialisasi grid dengan ukuran yang benar
        for ($col = 1; $col <= $this->total_columns; $col++) {
            $column = [];
            for ($row = 1; $row <= $this->rows_per_column; $row++) {
                // Cari question yang sesuai
                $question = $questions->firstWhere(function($q) use ($col, $row) {
                    return $q->column_number == $col && $q->row_number == $row;
                });
                
                $column[] = [
                    'row' => $row,
                    'value' => $question ? $question->value : rand(0, 9)
                ];
            }
            $grid[] = $column;
        }
        
        return $grid;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTotalQuestionsAttribute()
    {
        return $this->total_columns * ($this->rows_per_column - 1);
    }
}
