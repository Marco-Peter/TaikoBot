<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Models\Team;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description');
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('signout_limit')->default(7);
            $table->unsignedInteger('teacher_payment')->default(0);
            $table->unsignedInteger('assist_payment')->default(0);
            $table->timestamps();
        });

        Schema::create('compensations', function (Blueprint $table) {
            $table->foreignIdFor(Course::class, 'original_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignIdFor(Course::class, 'compensation_id')->constrained('courses')->cascadeOnDelete();
            $table->primary(['original_id', 'compensation_id']);
        });

        Schema::create('course_team', function (Blueprint $table) {
            $table->foreignIdFor(Course::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Team::class)->constrained()->cascadeOnDelete();
            $table->primary(['course_id', 'team_id']);
        });

        Schema::create('course_user', function (Blueprint $table) {
            $table->foreignIdFor(Course::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->primary(['course_id', 'user_id']);
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });
    
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Course::class)->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('name');
            $table->string('notes')->nullable();
            $table->boolean('external')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('course_user');
        Schema::dropIfExists('course_team');
        Schema::dropIfExists('compensations');
        Schema::dropIfExists('courses');
    }
};
