<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'test_id',
        'start_time',
        'end_time',
        'status',
        'score',
        'line_positions',
        'skipped_columns'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'line_positions' => 'array'
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function answers()
    {
        return $this->hasMany(TestAnswer::class);
    }

    public function calculateScore()
    {
        $correctAnswers = $this->answers()->where('is_correct', true)->count();
        $totalAttempt = $this->answers()->count();

        $this->score = $correctAnswers;
        $this->save();

        return [
            'correct' => $correctAnswers,
            'total_attempted' => $totalAttempt,
            'accuracy' => $totalAttempt > 0 ? ($correctAnswers / $totalAttempt) * 100 : 0
        ];
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
