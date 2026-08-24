<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryPhoto extends Model
{
    protected $fillable = [
        'device_id',
        'employee_name',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'taken_at',
        'status',
        'flag_note',
        'flagged_at',
        'flagged_by',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'datetime',
            'flagged_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFlagged(): bool
    {
        return $this->status === 'flagged';
    }

    public function isOk(): bool
    {
        return $this->status === 'ok';
    }

    public function getPublicUrlAttribute(): string
    {
        return url('storage/'.$this->file_path);
    }
}
