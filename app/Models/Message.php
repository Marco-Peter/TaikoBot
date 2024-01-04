<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
    ];

    /**
     * To which channel is this message posted?
     */
    public function messageChannel(): BelongsTo
    {
        return $this->belongsTo(MessageChannel::class);
    }

    /**
     * Which poster die write this message?
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all replies (if any) to a message
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the original message, if this is a reply
     */
    public function original(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
