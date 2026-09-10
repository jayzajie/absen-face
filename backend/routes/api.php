<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Endpoint untuk aplikasi mobile (HP kantor)
Route::post('/mobile/login', [AuthController::class, 'mobileLogin']);
Route::get('/attendances', [AttendanceController::class, 'index']);
Route::post('/attendances', [AttendanceController::class, 'store']);
Route::post('/attendances/verify', [AttendanceController::class, 'storeVerified']);
