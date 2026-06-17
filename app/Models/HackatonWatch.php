<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackatonWatch extends Model
{
    protected $fillable = [
        'user_id',
        'hackaton_id',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Hackaton, $this> */
    public function hackaton(): BelongsTo
    {
        return $this->belongsTo(Hackaton::class);
    }
}
