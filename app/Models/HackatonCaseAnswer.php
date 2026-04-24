<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackatonCaseAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'hackaton_case_submission_id',
        'hackaton_case_field_id',
        'value_text',
        'value_json',
        'file_path',
    ];

    protected function casts(): array
    {
        return [
            'value_json' => 'array',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(HackatonCaseSubmission::class, 'hackaton_case_submission_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(HackatonCaseField::class, 'hackaton_case_field_id');
    }
}
