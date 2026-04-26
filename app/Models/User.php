<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function hackatons(): HasMany
    {
        return $this->hasMany(Hackaton::class);
    }

    public function teamRoles(): HasMany
    {
        return $this->hasMany(TeamRole::class);
    }

    public function userDocuments(): HasMany
    {
        return $this->hasMany(UserHackatonDocument::class);
    }

    public function teamApplications(): HasMany
    {
        return $this->hasMany(TeamApplication::class);
    }

    public function caseSubmissions(): HasMany
    {
        return $this->hasMany(HackatonCaseSubmission::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(HackatonCertificate::class);
    }

    public function judgeAssignments(): HasMany
    {
        return $this->hasMany(HackatonJudge::class);
    }

    public function judgedHackatons(): BelongsToMany
    {
        return $this->belongsToMany(Hackaton::class, 'hackaton_judges')
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
    }

    public function sentJudgeInvitations(): HasMany
    {
        return $this->hasMany(JudgeInvitation::class, 'invited_by');
    }

    public function receivedJudgeInvitations(): HasMany
    {
        return $this->hasMany(JudgeInvitation::class, 'invited_user_id');
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
        return Str::of($this->fio)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'partner';
    }

    public function isJudge(): bool
    {
        return $this->role === 'judge';
    }

    public function isParticipant(): bool
    {
        return $this->role === 'user';
    }
}
