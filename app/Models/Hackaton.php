<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hackaton extends Model
{
    /** @use HasFactory<\Database\Factories\HackatonFactory> */
    use HasFactory;

    public function teams(): HasMany {
        return $this->hasMany(Team::class);
    }


    public function participantsCount() {
        return $this->teams()->count();
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany {
        return $this->hasMany(HackatonDocument::class);
    }

    public function usersDocuments(): HasMany {
        return $this->hasMany(UserHackatonDocument::class);
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_url',
        'start_at',
        'end_at',
        'is_public',
    ];

}
