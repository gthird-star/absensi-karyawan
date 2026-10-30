<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory, HasTimestamps;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'is_cross_midnight',
        'late_tolerance_minutes',
    ];

    protected function casts(): array
    {
        return [
            'is_cross_midnight' => 'boolean',
            'late_tolerance_minutes' => 'integer',
        ];
    }
}
