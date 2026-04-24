<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicCatalogController extends Controller
{
    public function hackatons(Request $request): JsonResponse
    {
        $hackatons = Hackaton::query()
            ->where('is_public', true)
            ->select(['id', 'title', 'start_at', 'end_at', 'image_url'])
            ->with(['images:id,hackaton_id,path,sort_order'])
            ->latest('id')
            ->paginate((int) $request->integer('per_page', 12));

        $hackatons->getCollection()->transform(function (Hackaton $hackaton) {
            $firstGalleryImage = $hackaton->images->first();

            $hackaton->setAttribute('gallery_preview', $firstGalleryImage?->path);
            $hackaton->setAttribute('gallery_count', $hackaton->images->count());

            return $hackaton;
        });

        return response()->json($hackatons);
    }

    public function teams(Request $request): JsonResponse
    {
        $teams = Team::query()
            ->where('is_public', true)
            ->select(['id', 'title', 'image_url', 'hackaton_id'])
            ->with('hackaton:id,title')
            ->latest('id')
            ->paginate((int) $request->integer('per_page', 12));

        return response()->json($teams);
    }

    public function profiles(Request $request): JsonResponse
    {
        $profiles = User::query()
            ->where('is_profile_public', true)
            ->select(['id', 'fio', 'nickname', 'role', 'description'])
            ->latest('id')
            ->paginate((int) $request->integer('per_page', 12));

        return response()->json($profiles);
    }
}
