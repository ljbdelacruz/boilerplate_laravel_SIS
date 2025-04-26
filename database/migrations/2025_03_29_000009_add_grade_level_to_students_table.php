<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('grade_level')->after('user_id');
            $table->foreignId('section_id')->nullable()->after('grade_level')->constrained()->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn(['grade_level', 'section_id']);
        });
    }
};