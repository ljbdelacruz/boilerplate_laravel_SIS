<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('module')->nullable()->after('description');
            $table->json('old_data')->nullable()->after('module');
            $table->json('new_data')->nullable()->after('old_data');
            $table->string('method')->nullable()->after('new_data');
            $table->string('url')->nullable()->after('method');
            $table->string('status')->nullable()->after('url');
        });
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['module', 'old_data', 'new_data', 'method', 'url', 'status']);
        });
    }
};