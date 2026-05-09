<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Support\InitialsGenerator;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

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
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            ->withPivot(['assigned_by', 'assigned_at'])
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
        'role',
        'is_profile_public',
        'show_email_on_profile',
        'show_phone_on_profile',
        'phone_verified_at',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'phone_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return InitialsGenerator::generate($this->fio);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isOrganizer(): bool
    {
        return $this->role === UserRole::PARTNER;
    }

    public function isJudge(): bool
    {
        return $this->role === UserRole::JUDGE;
    }

    public function isParticipant(): bool
    {
        return $this->role === UserRole::USER;
    }
}
