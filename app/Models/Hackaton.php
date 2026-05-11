<?php

namespace App\Models;

use App\Enums\HackatonLevel;
use App\Enums\HackatonStatus;
use Database\Factories\HackatonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $image_url
 * @property HackatonStatus $status
 * @property Carbon $start_at
 * @property Carbon $end_at
 * @property bool $is_public
 * @property string|null $gallery_preview
 * @property int|null $images_count
 * @property-read User|null $user
 */
class Hackaton extends Model
{
    /** @use HasFactory<HackatonFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::increment('api:v1:catalog:hackatons:version');
            if (Cache::supportsTags()) {
                Cache::tags(['catalog', 'home'])->flush();
            } else {
                Cache::forget('home-featured-hackatons-v2');
                Cache::forget('home-public-totals-v4');
            }
        });
        static::deleted(function () {
            Cache::increment('api:v1:catalog:hackatons:version');
            if (Cache::supportsTags()) {
                Cache::tags(['catalog', 'home'])->flush();
            } else {
                Cache::forget('home-featured-hackatons-v2');
                Cache::forget('home-public-totals-v4');
            }
        });
    }

    private const SHOW_RELATIONS = [
        'user',
        'documents',
        'teams.roles',
        'teams.user',
        'cases.fields',
        'cases.submissions.answers',
        'cases.submissions.score',
        'announcements.author',
        'announcements.images',
        'judges',
        'images',
    ];

    /** @return HasMany<Team, $this> */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function teamsCount(): int
    {
        return $this->teams()->count();
    }

    /** @return HasManyThrough<TeamRole, Team, $this> */
    public function roles(): HasManyThrough
    {
        return $this->hasManyThrough(TeamRole::class, Team::class);
    }

    public function participantsCount(): int
    {
        return $this->roles()->whereNotNull('team_roles.user_id')->count();
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<HackatonDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(HackatonDocument::class);
    }

    /** @return HasMany<UserHackatonDocument, $this> */
    public function usersDocuments(): HasMany
    {
        return $this->hasMany(UserHackatonDocument::class);
    }

    /** @return HasMany<HackatonApplication, $this> */
    public function applications(): HasMany
    {
        return $this->hasMany(HackatonApplication::class);
    }

    /** @return HasMany<HackatonCase, $this> */
    public function cases(): HasMany
    {
        return $this->hasMany(HackatonCase::class)->orderBy('sort_order');
    }

    /** @return HasMany<HackatonAnnouncement, $this> */
    public function announcements(): HasMany
    {
        return $this->hasMany(HackatonAnnouncement::class)->latest('published_at');
    }

    /** @return HasMany<HackatonCertificate, $this> */
    public function certificates(): HasMany
    {
        return $this->hasMany(HackatonCertificate::class)->latest('issued_at');
    }

    /** @return HasMany<HackatonImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(HackatonImage::class)->orderBy('sort_order');
    }

    /** @return HasMany<HackatonJudge, $this> */
    public function judgeAssignments(): HasMany
    {
        return $this->hasMany(HackatonJudge::class);
    }

    /** @return BelongsToMany<User, $this> */
    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hackaton_judges')
            ->withPivot(['assigned_by', 'assigned_at', 'domain'])
            ->withTimestamps();
    }

    /** @return HasMany<JudgeInvitation, $this> */
    public function judgeInvitations(): HasMany
    {
        return $this->hasMany(JudgeInvitation::class)->latest();
    }

    public function isJudge(User $user): bool
    {
        return $this->judges()->where('users.id', $user->id)->exists();
    }

    public function loadShowRelations(): self
    {
        $this->load(self::SHOW_RELATIONS);

        return $this;
    }

    public function syncStatusByTimeline(): bool
    {
        if ($this->status === HackatonStatus::ARCHIVED) {
            return false;
        }

        $now = now();
        $registrationDeadlineAt = $this->registration_deadline_at;
        $hasPublishedCases = $this->cases()->where('is_published', true)->exists();
        $daysBeforeStart = $now->diffInDays($this->start_at, false);

        $nextStatus = match (true) {
            ! $this->is_public => HackatonStatus::DRAFT,
            $now->lt($this->start_at) && ($registrationDeadlineAt === null || $now->lte($registrationDeadlineAt)) => HackatonStatus::REGISTRATION_OPEN,
            $now->lt($this->start_at) && $hasPublishedCases => HackatonStatus::CASES_ANNOUNCED,
            $now->lt($this->start_at) && $daysBeforeStart <= 2 => HackatonStatus::WAITING_START,
            $now->lt($this->start_at) => HackatonStatus::REGISTRATION_CLOSED,
            $now->between($this->start_at, $this->end_at) => HackatonStatus::IN_PROGRESS,
            $now->gt($this->end_at) && $this->status === HackatonStatus::JUDGING => HackatonStatus::JUDGING,
            default => HackatonStatus::FINISHED,
        };

        if ($this->status === $nextStatus) {
            return false;
        }

        $this->update(['status' => $nextStatus]);

        return true;
    }

    protected function casts(): array
    {
        return [
            'status' => HackatonStatus::class,
            'level' => HackatonLevel::class,
            'is_public' => 'boolean',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'registration_deadline_at' => 'datetime',
            'prize_fund' => 'decimal:2',
        ];
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_url',
        'start_at',
        'end_at',
        'is_public',
        'status',
        'prize_fund',
        'prize_places_count',
        'level',
        'registration_deadline_at',
    ];
}
