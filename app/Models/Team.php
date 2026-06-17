<?php

namespace App\Models;

use App\Support\InitialsGenerator;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * @property int $id
 * @property int $user_id
 * @property-read User|null $user
 * @property-read Collection<int, TeamRole> $roles
 */
class Team extends Model
{
    public const DEFAULT_TEAM_IMAGE_PATH = 'team_photos/default.png';

    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function (Team $team): void {
            if (
                ! $team->wasRecentlyCreated
                && ! $team->wasChanged(['title', 'is_public', 'image_url', 'hackaton_id'])
            ) {
                return;
            }

            if (Cache::supportsTags()) {
                Cache::tags(['catalog', 'home'])->flush();
            } else {
                Cache::forget('home-featured-hackatons-v2');
                Cache::forget('home-public-totals-v4');
            }
        });
        static::deleted(function (): void {
            if (Cache::supportsTags()) {
                Cache::tags(['catalog', 'home'])->flush();
            } else {
                Cache::forget('home-featured-hackatons-v2');
                Cache::forget('home-public-totals-v4');
            }
        });
    }

    private const SHOW_BASE_RELATIONS = [
        'user',
        'hackaton',
        'socialLinks',
        'roles.role',
        'roles.skills',
        'roles.user',
    ];

    private const SHOW_OWNER_RELATIONS = [
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

    /** @return HasMany<TeamRole, $this> */
    public function roles(): HasMany
    {
        return $this->hasMany(TeamRole::class);
    }

    public function captainRole(): ?TeamRole
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles
                ->first(fn (TeamRole $role): bool => $role->user_id === $this->user_id);
        }

        return TeamRole::query()
            ->whereCaptain($this)
            ->first();
    }

    /**
     * @param  array{title:mixed,description:mixed,role:mixed,skills?:mixed}  $captainRoleData
     */
    public function ensureCaptainHasRole(array $captainRoleData): TeamRole
    {
        if (! $this->exists) {
            throw new InvalidArgumentException('Captain role can only be ensured for a persisted team owner.');
        }

        $title = trim((string) ($captainRoleData['title'] ?? ''));
        $description = trim((string) ($captainRoleData['description'] ?? ''));
        $roleId = (int) ($captainRoleData['role'] ?? 0);
        $skills = collect($captainRoleData['skills'] ?? [])
            ->filter(fn (mixed $skillId): bool => filled($skillId))
            ->map(fn (mixed $skillId): int => (int) $skillId)
            ->values()
            ->all();

        if ($title === '' || $description === '' || $roleId === 0) {
            throw new InvalidArgumentException('Captain role data is incomplete.');
        }

        $captainRole = TeamRole::query()->firstOrNew([
            'team_id' => $this->id,
            'user_id' => $this->user_id,
        ]);

        $captainRole->fill([
            'title' => $title,
            'description' => $description,
            'role_id' => $roleId,
        ]);
        $captainRole->save();
        $captainRole->skills()->sync($skills);

        return $captainRole;
    }

    public function emptyRoles(): int
    {
        return $this->hasMany(TeamRole::class)->whereNull('user_id')->count();
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participantsCount(): int
    {
        return $this->roles()->whereNotNull('user_id')->count();
    }

    public function hasMember(User $user): bool
    {
        if ((int) $this->user_id === (int) $user->id) {
            return true;
        }

        if ($this->relationLoaded('roles')) {
            return $this->roles->contains(
                fn (TeamRole $role): bool => (int) $role->user_id === (int) $user->id
            );
        }

        return $this->roles()->where('user_id', $user->id)->exists();
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

    /** @return HasMany<TeamMessage, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(TeamMessage::class);
    }

    public function loadShowRelations(bool $includeOwnerRelations = false): self
    {
        $relations = self::SHOW_BASE_RELATIONS;
        if ($includeOwnerRelations) {
            $relations = [...$relations, ...self::SHOW_OWNER_RELATIONS];
        }

        $this->load($relations);

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
        'hackaton_case_id',
        'is_public',
    ];

    public function hackatonCase(): BelongsTo
    {
        return $this->belongsTo(HackatonCase::class, 'hackaton_case_id');
    }
}
