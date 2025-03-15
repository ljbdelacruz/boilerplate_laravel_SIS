<?php

use App\Http\Controllers\SchoolYearController;
use Illuminate\Support\Facades\Route;

Route::controller(SchoolYearController::class)->group(function () {
    Route::post('add-school-year', 'store');
    Route::get('school-years', 'index');
});