<?php

use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Set login as default route
Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::get('/about-us', function () {
    return view('about_us');
});

Route::get('/school-years-view', [SchoolYearController::class, 'index_view']);

// Auth Routes
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes
// Add these routes inside your auth middleware group
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('students', StudentController::class);
});