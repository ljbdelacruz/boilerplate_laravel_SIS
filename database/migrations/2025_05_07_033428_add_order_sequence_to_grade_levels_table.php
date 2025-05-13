<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('grade_levels', function (Blueprint $table) {
            $table->integer('order_sequence')->nullable()->after('grade_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('grade_levels', function (Blueprint $table) {
            $table->dropColumn('order_sequence');
        });
    }
};
