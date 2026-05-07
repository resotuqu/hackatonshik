<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    public function teamRoles(): BelongsToMany
    {
        return $this->belongsToMany(TeamRole::class, 'team_role_skills');
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected $fillable = [
        'name',
    ];
}
