<?php

use App\Http\Controllers\API\Auth\OTPController;
use App\Http\Controllers\API\DemoController;
use App\Http\Controllers\API\GenerateController;
use Illuminate\Support\Facades\Route;

// NO prefix here - it's already in api.php
Route::post('/send-otp', [OTPController::class, 'sendOTP']);
Route::post('/verify-otp', [OTPController::class, 'verifyOTP']);
Route::post('/resend-otp', [OTPController::class, 'resendOTP']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [OTPController::class, 'logout']);
    Route::get('/user', [OTPController::class, 'user']);
    Route::get('/stats', [GenerateController::class, 'stats']);
    Route::post('/generate', [GenerateController::class, 'generate']);
    Route::get('/history', [GenerateController::class, 'history']);
    Route::get('/history/{id}', [GenerateController::class, 'show']);
    Route::delete('/history/{id}', [GenerateController::class, 'destroy']);
    Route::post('/demo-generate', [DemoController::class, 'generate']);
});
