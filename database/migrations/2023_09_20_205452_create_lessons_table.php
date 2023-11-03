<?php

use App\Models\Course;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Course::class)->constrained()->cascadeOnDelete();

            /* Public information for students */
            $table->string('title', 255); /* Title or topic of the lesson */
            $table->dateTime('start');
            $table->dateTime('finish');

            /* Internal information for teachers and administration */
            $table->text('notes')->default(new Expression("('')")); /* Notes for teachers to organize the lesson */

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
