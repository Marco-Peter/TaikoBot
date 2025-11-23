<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedInteger('teacher_payment')->default(0)->after('signout_limit');
            $table->unsignedInteger('assist_payment')->default(0)->after('teacher_payment');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_payment')->default('2020-01-01 00:00:00')->after('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('teacher_payment');
            $table->dropColumn('assist_payment');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_payment');
        });
    }
};
