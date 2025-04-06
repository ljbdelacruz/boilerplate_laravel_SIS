<?php

use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherClassController;
use App\Http\Controllers\TeacherScheduleController;
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

Route::middleware(['auth'])->group(function () {
    Route::resource('teachers', TeacherController::class);
});
Route::middleware(['auth'])->group(function () {
    Route::resource('teacher-courses', TeacherCourseController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/teacher/classes', [TeacherClassController::class, 'index'])->name('teacher.classes');
    Route::get('/teacher/schedules/preferences', [TeacherScheduleController::class, 'preferences'])->name('teacher.schedules.preferences');
});


Route::middleware(['auth'])->group(function () {
    Route::resource('admin/students', StudentController::class)->names('admin.students');
    Route::post('admin/students/{student}/reset-password', [StudentController::class, 'resetPassword'])
        ->name('admin.students.reset-password');

    // Add batch upload routes
    Route::get('/admin/batch-upload/students', [UserController::class, 'uploadStudentsForm'])->name('students.upload');
    Route::post('/admin/batch-upload/students', [UserController::class, 'uploadStudents'])->name('students.upload.process');
    Route::get('/admin/batch-upload/teachers', [UserController::class, 'uploadTeachersForm'])->name('teachers.upload');
    Route::post('/admin/batch-upload/teachers', [UserController::class, 'uploadTeachers'])->name('teachers.upload.process');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherController::class, 'index'])->name('teacher.dashboard');
    Route::get('/teacher/view-students', [TeacherController::class, 'viewStudents'])->name('teacher.view.students');
});