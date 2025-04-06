<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeLevelController extends Model
{
    use HasFactory;

    protected $fillable = ['name']; // Assuming the grade level name is stored in a 'name' column
}