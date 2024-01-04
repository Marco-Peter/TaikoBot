<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'name',
        'notes',
        'external',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
