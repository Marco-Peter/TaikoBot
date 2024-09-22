<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lesson_user', function(Blueprint $table) {
            DB::statement("ALTER TABLE lesson_user MODIFY COLUMN participation ENUM('signed_out', 'signed_in', 'teacher', 'late', 'no_show', 'waitlist') NOT NULL");
            DB::statement("ALTER TABLE lesson_user ALTER COLUMN participation SET DEFAULT 'signed_in'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_user', function(Blueprint $table) {
            DB::statement("ALTER TABLE lesson_user MODIFY COLUMN participation ENUM('signed_out', 'signed_in', 'teacher', 'late', 'no_show') NOT NULL");
            DB::statement("ALTER TABLE lesson_user ALTER COLUMN participation SET DEFAULT 'signed_in'");
        });
    }
};
