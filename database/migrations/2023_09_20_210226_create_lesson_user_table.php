<?php

use App\Enums\LessonParticipationEnum;
use App\Models\Lesson;
use App\Models\User;
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
        Schema::create('lesson_user', function (Blueprint $table) {
            $table->foreignIdFor(Lesson::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->primary(['lesson_id', 'user_id']);
            $table->enum('participation', LessonParticipationEnum::values())->default(LessonParticipationEnum::SIGNED_IN);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_user');
    }
};
