<?php

use App\Http\Controllers\SchoolYearController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/about-us', function () {
    return view('about_us');
});

Route::get('/school-years-view', [SchoolYearController::class, 'index_view']);