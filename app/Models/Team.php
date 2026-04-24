<?php

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    private const SHOW_RELATIONS = [
        'user',
        'hackaton',
        'socialLinks',
        'roles.role',
        'roles.skills',
        'roles.user',
        'applications.user',
        'applications.teamRole.role',
        'applications.reviewer',
    ];

    public function socialLinks(): HasMany
    {
        return $this->hasMany(TeamSocialLink::class);
    }

    public function hackaton(): BelongsTo
    {
        return $this->belongsTo(Hackaton::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(TeamRole::class);
    }

    public function emptyRoles(): int
    {
        return $this->hasMany(TeamRole::class)->whereNull('user_id')->count();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participantsCount(): int
    {
        return $this->roles()->whereNotNull('user_id')->count();
    }

    public function applications(): HasManyThrough
    {
        return $this->hasManyThrough(TeamApplication::class, TeamRole::class);
    }

    public function hackatonApplications(): HasMany
    {
        return $this->hasMany(HackatonApplication::class);
    }

    public function caseSubmissions(): HasMany
    {
        return $this->hasMany(HackatonCaseSubmission::class);
    }

    public function loadShowRelations(): self
    {
        $this->load(self::SHOW_RELATIONS);

        return $this;
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
