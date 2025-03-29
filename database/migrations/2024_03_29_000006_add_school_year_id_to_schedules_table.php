<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Get the first school year ID before any operations
        $firstSchoolYear = DB::table('school_years')->first();

        if (!Schema::hasColumn('schedules', 'school_year_id')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->unsignedBigInteger('school_year_id')->after('course_id')->nullable();
            });

            if ($firstSchoolYear) {
                // Update existing records with the first school year
                DB::table('schedules')->update(['school_year_id' => $firstSchoolYear->id]);
            }

            Schema::table('schedules', function (Blueprint $table) {
                $table->foreign('school_year_id')
                      ->references('id')
                      ->on('school_years')
                      ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['school_year_id']);
            $table->dropColumn('school_year_id');
        });
    }
};