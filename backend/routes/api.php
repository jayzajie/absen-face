<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;

// Endpoint untuk aplikasi mobile (HP kantor)
Route::post('/mobile/login', [App\Http\Controllers\AuthController::class, 'mobileLogin']);
Route::get('/attendances',  [AttendanceController::class, 'index']);
Route::post('/attendances', [AttendanceController::class, 'store']);
Route::post('/gallery',     [GalleryController::class, 'upload']);
