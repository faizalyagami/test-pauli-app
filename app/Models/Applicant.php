<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'participant_numb',
        'full_name',
        'date_of_birth',
        'gender',
        'address',
        'phone',
        'education_background',
        'registration_date',
        'status'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'registration_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function testSessions()
    {
        return $this->hasMany(TestSession::class);
    }

    public function getLatestTestSession()
    {
        return $this->testSessions()->latest()->first();
    }
}
