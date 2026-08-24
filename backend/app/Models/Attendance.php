<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_name', 'type', 'occurred_at', 'status',
        'source', 'device_id', 'photo_access_granted',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'photo_access_granted' => 'boolean',
        ];
    }
}
