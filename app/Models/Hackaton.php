<?php

namespace App\Models;

use Database\Factories\HackatonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hackaton extends Model
{
    /** @use HasFactory<HackatonFactory> */
    use HasFactory;

    private const SHOW_RELATIONS = [
        'user',
        'documents',
        'teams.roles',
        'teams.user',
        'applications.team',
        'applications.reviewer',
        'cases.fields',
        'cases.submissions.answers',
        'cases.submissions.score',
        'announcements.author',
        'certificates.user',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function teamsCount(): int
    {
        return $this->teams()->count();
    }

    public function participantsCount(): int
    {
        return $this->teams()
            ->withCount(['roles as participants_count' => fn ($query) => $query->whereNotNull('user_id')])
            ->get()
            ->sum('participants_count');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(HackatonDocument::class);
    }

    public function usersDocuments(): HasMany
    {
        return $this->hasMany(UserHackatonDocument::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(HackatonApplication::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(HackatonCase::class)->orderBy('sort_order');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(HackatonAnnouncement::class)->latest('published_at');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(HackatonCertificate::class)->latest('issued_at');
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
        'start_at',
        'end_at',
        'is_public',
    ];
}
