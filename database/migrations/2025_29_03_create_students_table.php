<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('address');
            $table->string('contact_number')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('guardian_name');
            $table->string('guardian_contact');
            $table->foreignId('school_year_id')->constrained('school_years');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};