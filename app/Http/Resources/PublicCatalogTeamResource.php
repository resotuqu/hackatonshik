<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Hackaton;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Team
 */
class PublicCatalogTeamResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image_url' => $this->image_url,
            'hackaton_id' => $this->hackaton_id,
            'hackaton' => $this->whenLoaded('hackaton', function () {
                $hackaton = $this->hackaton;
                if (! $hackaton instanceof Hackaton) {
                    return null;
                }

                return [
                    'id' => $hackaton->id,
                    'title' => $hackaton->title,
                ];
            }),
        ];
    }
}
