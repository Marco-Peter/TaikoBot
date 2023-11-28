<?php

use App\Models\Course;
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
        Schema::create('compensations', function (Blueprint $table) {
            $table->foreignIdFor(Course::class, 'original_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignIdFor(Course::class, 'compensation_id')->constrained('courses')->cascadeOnDelete();
            $table->primary(['original_id', 'compensation_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compensations');
    }
};
