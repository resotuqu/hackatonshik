<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    public function socialLinks(): HasMany {
        return $this->hasMany(TeamSocialLink::class);
    }

    public function hackaton(): BelongsTo {
        return $this->belongsTo(Hackaton::class);
    }

    public function roles(): HasMany {
        return $this->hasMany(TeamRole::class);
    }

    public function emptyRoles(): int {
        return $this->hasMany(TeamRole::class)->whereNull('user_id')->count();
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function participantsCount() {
        return $this->roles()->where('user_id', '!=', null)->count();
    }


    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_url',
        'hackaton_id',
        'is_public',
    ];
}
