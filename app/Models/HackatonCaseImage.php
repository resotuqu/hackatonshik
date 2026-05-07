<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackatonCaseImage extends Model
{
    protected $fillable = [
        'hackaton_case_id',
        'path',
        'alt',
        'sort_order',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(HackatonCase::class, 'hackaton_case_id');
    }
}
