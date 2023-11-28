<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Fee extends Pivot
{
    protected $table = 'fees';

    public function incomeGroups(): BelongsTo
    {
        return $this->belongsTo(IncomeGroup::class);
    }

    public function courses(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
