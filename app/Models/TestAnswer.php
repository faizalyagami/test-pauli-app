<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_session_id',
        'question_id',
        'column_number',
        'row_number',
        'answer_value',
        'correct_value',
        'is_correct',
        'is_revised',
        'revised_count',
        'time_taken_seconds',
        'line_marker'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'is_revised' => 'boolean',
    ];

    public function testSession()
    {
        return $this->belongsTo(TestSession::class);
    }

    public function question()
    {
        return $this->belongsTo(PauliQuestion::class, 'question_id');
    }
}
