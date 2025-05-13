<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sections')) {
            Schema::create('sections', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('grade_level');
                $table->foreignId('adviser_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('school_year_id')->constrained()->onDelete('cascade');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            Schema::table('sections', function (Blueprint $table) {
                if (!Schema::hasColumn('sections', 'grade_level')) {
                    $table->string('grade_level')->after('name');
                }
                if (!Schema::hasColumn('sections', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('school_year_id');
                }
                if (!Schema::hasColumn('sections', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sections');
    }
};