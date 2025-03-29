<?php

use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(SchoolYearController::class)->group(function () {
    Route::post('add-school-year', 'store');
    Route::get('school-years', 'index');
});

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});