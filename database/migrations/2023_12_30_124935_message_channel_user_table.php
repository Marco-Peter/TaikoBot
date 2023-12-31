<?php

use App\Models\MessageChannel;
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
        Schema::create('message_channel_user', function (Blueprint $table)
        {
            $table->foreignIdFor(MessageChannel::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->primary(['message_channel_id', 'user_id']);
            $table->timestamp('read_until')->useCurrent();
            $table->boolean('can_post')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_channel_user');
    }
};
