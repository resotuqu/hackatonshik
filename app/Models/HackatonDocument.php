<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HackatonDocument extends Model
{
    /** @use HasFactory<\Database\Factories\HackatonDocumentFactory> */
    use HasFactory;

    public function hackaton(): BelongsTo {
        return $this->belongsTo(Hackaton::class);
    }

    public function usersFiles(): HasMany {
        return $this->HasMany(UserHackatonDocument::class);
    }

    protected $fillable = [
        'hackaton_id',
        'name',
        'description',
        'file_url',
        'filling_by_team_member',
    ];
}
