<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes — Laksana Backend
|--------------------------------------------------------------------------
*/

// ─── Public routes (tidak butuh token) ───────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ─── Protected routes (butuh Bearer token dari Sanctum) ──────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::get('/me',       [AuthController::class, 'me']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    // Laporan — semua user bisa baca dan buat
    Route::get('/reports',            [ReportController::class, 'index']);
    Route::post('/reports',           [ReportController::class, 'store']);
    Route::get('/my-reports',         [ReportController::class, 'myReports']);
    Route::delete('/reports/{id}',    [ReportController::class, 'destroy']);

    // Admin-only: update status & statistik
    Route::put('/reports/{id}/status', [ReportController::class, 'updateStatus']);
    Route::get('/admin/stats',         [ReportController::class, 'adminStats']);
});