<?php

use App\Enums\UserRoleEnum;
use App\Models\Team;
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
        Schema::table('users', function (Blueprint $table) {
            $table->json('settings')->nullable()->after('remember_token');
            $table->enum('role', UserRoleEnum::values())->default(UserRoleEnum::STUDENT->value)->after('settings');
            $table->integer('karma')->nullable()->after('role');
            $table->foreignIdFor(Team::class)->nullable()->after('karma')->constrained()->cascadeOnDelete();
            $table->string('profile_photo_path', 2048)->nullable()->after('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_photo_path', 'team_id', 'karma', 'role', 'settings']);
        });
    }
};
