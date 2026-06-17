<?php

namespace App\Models;

use App\Actions\Scoring\AggregateSubmissionScoresAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HackatonCaseSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'hackaton_case_id',
        'team_id',
        'user_id',
        'submitted_by_user_id',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<HackatonCase, $this> */
    public function case(): BelongsTo
    {
        return $this->belongsTo(HackatonCase::class, 'hackaton_case_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(HackatonCaseAnswer::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(HackatonCaseScore::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(HackatonCaseScore::class)->latestOfMany('reviewed_at');
    }

    /**
     * @return array{score: int, max_score: int}
     */
    public function aggregatedScoreTotals(): array
    {
        return app(AggregateSubmissionScoresAction::class)->forSubmission($this);
    }
}
