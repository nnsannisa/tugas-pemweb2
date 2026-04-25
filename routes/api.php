<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;

// ========================
// ROUTES AUTENTIKASI
// ========================
Route::post('/login', [AuthController::class, 'login']);

// ========================
// ROUTES YANG BUTUH LOGIN
// ========================
Route::middleware('auth:sanctum')->group(function () {

    // Profil & Logout
    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Routes mahasiswa - bisa diakses admin dan user
    Route::get('/students',      [StudentController::class, 'index']);
    Route::post('/students',     [StudentController::class, 'store']);
    Route::put('/students/{id}', [StudentController::class, 'update']);

    // Routes mahasiswa - hanya admin yang bisa delete
    Route::middleware('admin')->group(function () {
        Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    });
});
