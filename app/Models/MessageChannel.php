<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MessageChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Return all recipients of this message channel
     */
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['read_until']);
    }

    /**
     * Return the course, which this channel belongs to (if available).
     */
    public function course(): HasOne
    {
        return $this->hasOne(Course::class);
    }

    /**
     * Return the owner of the channel, if it is a user.
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
