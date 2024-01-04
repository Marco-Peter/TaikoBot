<?php

use App\Models\Message;
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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            /* To which message channel does this message go?
               Can be null, if it is a reaction to another message (thread)
            */
            $table->foreignIdFor(MessageChannel::class)
                ->nullable()->constrained()->cascadeOnDelete();

            /* Does this question answer to another one? */
            $table->foreignIdFor(Message::class)
                ->nullable()->constrained()->cascadeOnDelete();

            /* Who sent this message */
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
