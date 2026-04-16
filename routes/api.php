<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

// routes/api.php
Route::prefix('students')->group(function () {
    Route::get('/',          [StudentController::class, 'index']);   // Tampilkan Data
    Route::post('/',         [StudentController::class, 'store']);   // Tambah Data
    Route::put('/{id}',      [StudentController::class, 'update']);  // Ubah Data
    Route::delete('/{id}',   [StudentController::class, 'destroy']); // Hapus Data
});
