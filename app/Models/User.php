<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Support\InitialsGenerator;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property string $fio
 * @property string $email
 * @property UserRole $role
 * @property string|null $phone
 * @property Carbon|null $phone_verified_at
 * @property Carbon|null $email_verified_at
 * @property string|null $avatar_path
 */
class User extends Authenticatable implements FilamentUser, HasName, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, LogsActivity, Notifiable, TwoFactorAuthenticatable;

    protected static function booted(): void
    {
        static::saved(fn () => Cache::increment('api:v1:catalog:profiles:version'));
        static::deleted(fn () => Cache::increment('api:v1:catalog:profiles:version'));
    }

    /** @return HasMany<Team, $this> */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /** @return HasMany<Hackaton, $this> */
    public function hackatons(): HasMany
    {
        return $this->hasMany(Hackaton::class);
    }

    /** @return HasMany<TeamRole, $this> */
    public function teamRoles(): HasMany
    {
        return $this->hasMany(TeamRole::class);
    }

    /** @return HasMany<UserHackatonDocument, $this> */
    public function userDocuments(): HasMany
    {
        return $this->hasMany(UserHackatonDocument::class);
    }

    /** @return HasMany<TeamApplication, $this> */
    public function teamApplications(): HasMany
    {
        return $this->hasMany(TeamApplication::class);
    }

    /** @return HasMany<HackatonCaseSubmission, $this> */
    public function caseSubmissions(): HasMany
    {
        return $this->hasMany(HackatonCaseSubmission::class);
    }

    /** @return HasMany<HackatonCertificate, $this> */
    public function certificates(): HasMany
    {
        return $this->hasMany(HackatonCertificate::class);
    }

    /** @return HasMany<HackatonJudge, $this> */
    public function judgeAssignments(): HasMany
    {
        return $this->hasMany(HackatonJudge::class);
    }

    /** @return BelongsToMany<Hackaton, $this> */
    public function judgedHackatons(): BelongsToMany
    {
        return $this->belongsToMany(Hackaton::class, 'hackaton_judges')
            ->withPivot(['assigned_by', 'assigned_at', 'domain'])
            ->withTimestamps();
    }

    /** @return HasMany<JudgeInvitation, $this> */
    public function sentJudgeInvitations(): HasMany
    {
        return $this->hasMany(JudgeInvitation::class, 'invited_by');
    }

    /** @return HasMany<JudgeInvitation, $this> */
    public function receivedJudgeInvitations(): HasMany
    {
        return $this->hasMany(JudgeInvitation::class, 'invited_user_id');
    }

    /** @return HasMany<TeamMessage, $this> */
    public function teamMessages(): HasMany
    {
        return $this->hasMany(TeamMessage::class);
    }

    /** @return BelongsToMany<Skill, $this> */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    /** @return BelongsToMany<Hackaton, $this> */
    public function watchedHackatons(): BelongsToMany
    {
        return $this->belongsToMany(Hackaton::class, 'hackaton_watches')->withTimestamps();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fio',
        'date_of_birth',
        'email',
        'description',
        'nickname',
        'password',
        'phone',
        'is_profile_public',
        'show_email_on_profile',
        'show_phone_on_profile',
        'open_to_teams',
        'show_skills_on_profile',
        'avatar_path',
        'locale',
        'pd_consent_accepted_at',
        'oauth_provider',
        'oauth_provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_profile_public' => 'boolean',
            'show_email_on_profile' => 'boolean',
            'show_phone_on_profile' => 'boolean',
            'open_to_teams' => 'boolean',
            'show_skills_on_profile' => 'boolean',
            'phone_verified_at' => 'datetime',
            'suspended_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'pd_consent_accepted_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return InitialsGenerator::generate($this->fio);
    }

    /**
     * Get the user's anonymized display name: "Имя О.Ф."
     * Used for public-facing displays to protect privacy.
     */
    public function publicName(): string
    {
        if (! filled($this->fio)) {
            return $this->nickname ?? 'Участник';
        }

        $parts = preg_split('/\s+/u', trim($this->fio), 3);
        $lastName = $parts[0] ?? '';
        $firstName = $parts[1] ?? '';
        $patronymic = $parts[2] ?? '';

        if (! filled($firstName)) {
            return $lastName;
        }

        $initials = '';
        if (filled($patronymic)) {
            $initials .= mb_strtoupper(mb_substr($patronymic, 0, 1, 'UTF-8'), 'UTF-8').'.';
        }
        if (filled($lastName)) {
            $initials .= mb_strtoupper(mb_substr($lastName, 0, 1, 'UTF-8'), 'UTF-8').'.';
        }

        return $firstName.($initials ? ' '.$initials : '');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, [UserRole::ADMIN, UserRole::MODERATOR], true);
    }

    public function getFilamentName(): string
    {
        return $this->fio;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isModerator(): bool
    {
        return $this->role === UserRole::MODERATOR;
    }

    public function isAdminOrModerator(): bool
    {
        return $this->isAdmin() || $this->isModerator();
    }

    public function isOrganizer(): bool
    {
        return $this->role === UserRole::PARTNER;
    }

    public function isJudge(): bool
    {
        return $this->role === UserRole::JUDGE;
    }

    public function isAssignedJudge(): bool
    {
        if ($this->relationLoaded('judgeAssignments')) {
            return $this->judgeAssignments->isNotEmpty();
        }

        return $this->judgeAssignments()->exists();
    }

    public function isParticipant(): bool
    {
        return $this->role === UserRole::USER;
    }

    public function canParticipate(): bool
    {
        return $this->isParticipant() && ! $this->isSuspended();
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function hasVerifiedContactChannels(): bool
    {
        return $this->email_verified_at !== null && $this->phone_verified_at !== null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly(['fio', 'nickname', 'description', 'role'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
