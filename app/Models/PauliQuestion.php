<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PauliQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'column_number',
        'row_number',
        'value'
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
