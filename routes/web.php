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
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

// Set login as default route
Route::get('/', [AuthController::class, 'showLogin'])->name('login');


Route::get('/school-years-view', [SchoolYearController::class, 'index_view']);

 
 
// Auth Routes
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Section routes
Route::resource('sections', SectionController::class)->except(['show', 'destroy']);
Route::get('/sections/create', [SectionController::class, 'create'])->name('sections.create');
Route::put('sections/{section}/archive', [SectionController::class, 'archive'])->name('sections.archive');
Route::get('sections/archived', [SectionController::class, 'archivedIndex'])->name('sections.archivedIndex');
Route::put('sections/{id}/restore', [SectionController::class, 'restore'])->name('sections.restore');
Route::get('/sections/{section}/assign-students', [SectionController::class, 'showAssignStudentsForm'])->name('sections.assignStudentsForm');
Route::post('/sections/{section}/assign-students', [SectionController::class, 'assignStudents'])->name('sections.assignStudents');


// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/index', [DashboardController::class, 'showIndexPage'])->name('dashboard.index');


    Route::resource('school-years', SchoolYearController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('curriculums', CurriculumController::class);
    Route::put('courses/{course}/archive', [CourseController::class, 'archive'])->name('courses.archive');

    // Student Routes
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::get('/students/records', [StudentController::class, 'records'])->name('students.records');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    // Routes for unarchiving and archiving students
    Route::get('/admin/students/archived_index', [StudentController::class, 'archivedIndex'])
        ->name('admin.students.archivedIndex');
    Route::get('/admin/students/archived/{student_id}', [StudentController::class, 'archivedShow'])
        ->name('admin.students.archivedShow');
    Route::patch('/admin/students/{student_id}/unarchive', [StudentController::class, 'unarchive'])
        ->name('admin.students.unarchive');
    Route::resource('students', StudentController::class);

    // Student Course Routes
    Route::get('/api/courses-by-grade-level', [CourseController::class, 'getCoursesByGradeLevel'])->name('api.courses.byGradeLevel');
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
    Route::post('/save-grades/{student}', [TeacherController::class, 'saveGrades'])->name('teacher.save.grades');
    Route::get('/teacher/student/{student}/sf10', [TeacherController::class, 'showSF10'])->name('teacher.student.sf10');
    Route::post('/teacher/save-sf10-grades/{student}', [TeacherController::class, 'saveSF10'])->name('teacher.saveSF10');
    Route::get('/teacher/export-sf10/{student}', [TeacherController::class, 'exportSF10'])->name('teacher.export.sf10');
    Route::post('/teacher/handle-excel-upload/{student}', [TeacherController::class, 'handleExcelUpload'])
        ->name('teacher.handle-excel-upload');
    Route::post('/students/{student}/lvlup', [StudentController::class, 'levelUp'])
        ->name('students.lvlup')->middleware('auth');
    Route::post('/students/batch-lvlup', [StudentController::class, 'batchLevelUp'])
        ->name('students.batch-lvlup')->middleware('auth');
    Route::get('schedules/auto-generate', [ScheduleController::class, 'autoGenerateForm'])->name('schedules.auto-generate-form');
    Route::post('schedules/auto-generate', [ScheduleController::class, 'autoGenerate'])->name('schedules.auto-generate');
    Route::resource('curriculums', CurriculumController::class);
    Route::get('/sections/{section}/curriculum', [SectionController::class, 'getCurriculum'])->name('section.curriculum');
    Route::get('/curriculums/{curriculum}/edit', [CurriculumController::class, 'edit'])->name('curriculums.edit');
    Route::delete('/curriculums/{curriculum}', [CurriculumController::class, 'destroy'])->name('curriculums.destroy');

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