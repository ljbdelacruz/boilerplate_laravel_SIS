<?php

use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Set login as default route
Route::get('/', [AuthController::class, 'showLogin'])->name('login');


Route::get('/school-years-view', [SchoolYearController::class, 'index_view']);

// Auth Routes
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('students', StudentController::class);
    Route::resource('school-years', SchoolYearController::class);
    Route::resource('courses', CourseController::class);
    Route::put('courses/{course}/archive', [CourseController::class, 'archive'])->name('courses.archive');

    // Student Course Routes
    Route::get('/student/courses/available', [StudentCourseController::class, 'available'])
        ->name('student.courses.available');
    Route::get('/student/courses/enrolled', [StudentCourseController::class, 'enrolled'])
        ->name('student.courses.enrolled');
    Route::post('/student/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])
        ->name('student.courses.enroll');
    Route::get('/student/payments', [StudentPaymentController::class, 'index'])->name('student.payments');
    // Add this inside your admin middleware group
    Route::resource('users', UserController::class)->except(['show', 'destroy']);
});