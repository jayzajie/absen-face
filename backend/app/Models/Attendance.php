<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_name', 'type', 'occurred_at', 'status',
        'source', 'device_id', 'photo_access_granted',
        'face_match_score', 'face_threshold', 'face_model_version',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'photo_access_granted' => 'boolean',
            'face_match_score' => 'float',
            'face_threshold' => 'float',
        ];
    }
}
