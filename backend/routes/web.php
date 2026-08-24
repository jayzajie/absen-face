<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;

// ── Auth (tanpa middleware, publik) ─────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── HR Dashboard (harus login dulu) ────────────────
Route::middleware('hr')->group(function () {
    // Dashboard absensi
    Route::get('/', [AttendanceController::class, 'dashboard'])->name('dashboard');
    Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

    // Kelola karyawan
    Route::post('/employees', [App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
    Route::delete('/employees/{employee}', [App\Http\Controllers\EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Galeri kontrol HP kantor
    Route::post('/gallery/{photo}/flag',    [GalleryController::class, 'flag'])->name('gallery.flag');
    Route::post('/gallery/{photo}/approve', [GalleryController::class, 'approve'])->name('gallery.approve');
    Route::get('/gallery/download-zip',     [GalleryController::class, 'downloadZip'])->name('gallery.download-zip');
    Route::delete('/gallery/{photo}',       [GalleryController::class, 'destroy'])->name('gallery.destroy');
});
