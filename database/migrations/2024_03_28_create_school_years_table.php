<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('school_year');
            $table->string('grade_level');
            $table->string('section_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('school_years');
    }
};