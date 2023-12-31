<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        return $this->belongsToMany(User::class);
    }
}
