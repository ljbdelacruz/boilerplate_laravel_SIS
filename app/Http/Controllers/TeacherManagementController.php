<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TeacherManagementController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'teacher')
                       ->orderBy('name')
                       ->get();
        
        return view('admin.teachers.index', compact('teachers'));
    }
}