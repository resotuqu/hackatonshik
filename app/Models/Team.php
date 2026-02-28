<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    public function socialLinks(): HasMany {
        return $this->hasMany(TeamSocialLink::class);
    }

    public function roles(): HasMany {
        return $this->hasMany(Role::class);
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
