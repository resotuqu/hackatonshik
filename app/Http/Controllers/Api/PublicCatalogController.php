<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicCatalogHackatonResource;
use App\Http\Resources\PublicCatalogProfileResource;
use App\Http\Resources\PublicCatalogTeamResource;
use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicCatalogController extends Controller
{
    public function hackatons(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 12), 1), 60);
        $upcoming = $request->boolean('upcoming');
        $page = max($request->integer('page', 1), 1);

        $buildPaginator = function () use ($perPage, $upcoming, $page): LengthAwarePaginator {
            return Hackaton::query()
                ->where('is_public', true)
                ->select(['id', 'title', 'start_at', 'end_at', 'image_url'])
                ->with(['images:id,hackaton_id,path,sort_order'])
                ->when($upcoming, fn ($query) => $query->where('start_at', '>=', now()->startOfDay()))
                ->latest('id')
                ->paginate($perPage, ['*'], 'page', $page);
        };

        if (app()->isProduction()) {
            $cacheKey = sprintf('api:v1:catalog:hackatons:p%d:u%d:pg%d', $perPage, (int) $upcoming, $page);
            $hackatons = Cache::remember($cacheKey, 30, $buildPaginator);
        } else {
            $hackatons = $buildPaginator();
        }

        return PublicCatalogHackatonResource::collection($hackatons)->response();
    }

    public function teams(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 12), 1), 60);

        $teams = Team::query()
            ->where('is_public', true)
            ->select(['id', 'title', 'image_url', 'hackaton_id'])
            ->with('hackaton:id,title')
            ->latest('id')
            ->paginate($perPage);

        return PublicCatalogTeamResource::collection($teams)->response();
    }

    public function profiles(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 12), 1), 60);

        $profiles = User::query()
            ->where('is_profile_public', true)
            ->select(['id', 'fio', 'nickname', 'role', 'description'])
            ->latest('id')
            ->paginate($perPage);

        return PublicCatalogProfileResource::collection($profiles)->response();
    }
}
