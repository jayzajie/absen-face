<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'username', 'password', 'device_id', 'face_photo_path'];

    protected $hidden = ['password', 'face_photo_path'];
}
