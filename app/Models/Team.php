<?php

namespace App\Models;

use App\Support\InitialsGenerator;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Team extends Model
{
    public const DEFAULT_TEAM_IMAGE_PATH = 'team_photos/default.png';

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

    /**
     * Public URL for list card cover photo, or null to show gradient placeholder.
     * Dedicated cover_image wins; otherwise image_url unless it is the generic default.
     */
    public function coverImagePublicUrl(): ?string
    {
        if (filled($this->cover_image) && ! $this->isDefaultTeamImagePath((string) $this->cover_image)) {
            return $this->resolveStorageOrAbsoluteUrl((string) $this->cover_image);
        }

        if (! filled($this->image_url) || $this->isDefaultTeamImagePath((string) $this->image_url)) {
            return null;
        }

        return $this->resolveStorageOrAbsoluteUrl((string) $this->image_url);
    }

    /**
     * Short initials from team title (Cyrillic-safe) for cover gradient.
     */
    public function initialsForCover(): string
    {
        return InitialsGenerator::generate($this->title);
    }

    private function isDefaultTeamImagePath(string $path): bool
    {
        return $path === self::DEFAULT_TEAM_IMAGE_PATH
            || str_ends_with($path, '/'.self::DEFAULT_TEAM_IMAGE_PATH);
    }

    private function resolveStorageOrAbsoluteUrl(string $path): string
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return asset('storage/'.$path);
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

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_url',
        'cover_image',
        'hackaton_id',
        'is_public',
    ];
}
