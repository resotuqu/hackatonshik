<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Hackaton;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Hackaton
 */
class PublicCatalogHackatonResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start_at' => $this->start_at->toIso8601String(),
            'end_at' => $this->end_at->toIso8601String(),
            'image_url' => $this->image_url,
            'gallery_preview' => $this->gallery_preview,
            'gallery_count' => (int) ($this->images_count ?? 0),
        ];
    }
}
