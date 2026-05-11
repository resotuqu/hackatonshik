<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackatonCaseScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'hackaton_case_submission_id',
        'reviewed_by',
        'score',
        'max_score',
        'criteria_scores',
        'field_comments',
        'general_comment',
        'is_final',
        'draft_saved_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'draft_saved_at' => 'datetime',
            'criteria_scores' => 'array',
            'field_comments' => 'array',
            'is_final' => 'boolean',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(HackatonCaseSubmission::class, 'hackaton_case_submission_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
