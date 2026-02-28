<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TeamRole extends Model
{
    /** @use HasFactory<\Database\Factories\TeamRoleFactory> */
    use HasFactory;

    public function skills(): HasMany {
        return $this->hasMany(Skill::class);
    }

    public function user(): HasOne {
        return $this->hasOne(User::class);
    }

    protected $fillable = [
        'title',
        'description',
        'team_id',
        'role_id',
        'user_id',
    ];

}
