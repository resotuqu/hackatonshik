<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHackatonDocument extends Model
{
    /** @use HasFactory<\Database\Factories\UserHackatonDocumentFactory> */
    use HasFactory;

    public function hackatonDocument(): BelongsTo {
        return $this->belongsTo(HackatonDocument::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'user_id',
        'hackaton_document_id',
        'file_url',
    ];

}
