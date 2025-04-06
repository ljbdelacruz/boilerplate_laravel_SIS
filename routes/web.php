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
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

// Set login as default route
Route::get('/', [AuthController::class, 'showLogin'])->name('login');


Route::get('/school-years-view', [SchoolYearController::class, 'index_view']);

// Auth Routes
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Section routes
Route::resource('sections', SectionController::class);
Route::put('sections/{section}/archive', [SectionController::class, 'archive'])->name('sections.archive');

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
    Route::get('/teacher/submit-grades/{student}', [TeacherController::class, 'submitGrades'])->name('teacher.submit.grades');
    Route::get('/teacher/student/{student}/sf10', [TeacherController::class, 'showSF10'])->name('teacher.student.sf10');
    Route::get('/teacher/export-sf10/{student}', [TeacherController::class, 'exportSF10'])->name('teacher.export.sf10');
    Route::post('/teacher/handle-excel-upload/{student}', [TeacherController::class, 'handleExcelUpload'])
        ->name('teacher.handle-excel-upload');
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
    Route::get('/teachers', [TeacherController::class, 'list'])->name('teachers.index');
    Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
});


Route::prefix('schedules')->group(function () {
    Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/generate', [ScheduleController::class, 'showGenerateForm'])->name('schedules.generate');
    Route::post('/generate', [ScheduleController::class, 'generateSchedule']);
    Route::get('/create', [ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/store', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/manage', [ScheduleController::class, 'manage'])->name('schedules.manage');
    Route::get('/edit/{schedule}', [ScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/update/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/delete/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
});

Route::patch('/school-years/{schoolYear}/toggle-active', [SchoolYearController::class, 'toggleActive'])
    ->name('school-years.toggle-active');

Route::middleware(['auth'])->group(function () {
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/user/{id}', [ActivityLogController::class, 'userLogs'])->name('activity-logs.user');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/teacher/schedule', [TeacherController::class, 'schedule'])->name('teacher.schedule');
});