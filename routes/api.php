<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/reports', [ReportController::class, 'index']);       // Ambil semua laporan
    Route::post('/reports', [ReportController::class, 'store']);      // Kirim laporan baru
    
    Route::patch('/reports/{id}/status', [ReportController::class, 'updateStatus']); 
    
});