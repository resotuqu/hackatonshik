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
use Illuminate\Contracts\Cache\Repository as CacheRepository;
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
                ->withCount('images')
                ->selectSub(
                    fn (\Illuminate\Database\Query\Builder $query) => $query
                        ->from('hackaton_images')
                        ->whereColumn('hackaton_images.hackaton_id', 'hackatons.id')
                        ->orderBy('sort_order')
                        ->limit(1)
                        ->select('path'),
                    'gallery_preview'
                )
                ->when($upcoming, fn ($query) => $query->where('start_at', '>=', now()->startOfDay()))
                ->latest('id')
                ->paginate($perPage, ['*'], 'page', $page);
        };

        if (app()->isProduction()) {
            $catalogCache = $this->cacheRepository(['catalog', 'catalog:hackatons']);
            $catalogVersion = (int) $catalogCache->get('api:v1:catalog:hackatons:version', 1);
            $cacheKey = sprintf('api:v1:catalog:hackatons:v%d:p%d:u%d:pg%d', $catalogVersion, $perPage, (int) $upcoming, $page);
            $hackatons = $catalogCache->remember($cacheKey, 30, $buildPaginator);
        } else {
            $hackatons = $buildPaginator();
        }

        return PublicCatalogHackatonResource::collection($hackatons)->response();
    }

    public function teams(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 12), 1), 60);
        $page = max($request->integer('page', 1), 1);

        $buildPaginator = fn (): LengthAwarePaginator => Team::query()
            ->where('is_public', true)
            ->select(['id', 'title', 'image_url', 'hackaton_id'])
            ->with('hackaton:id,title')
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);

        if (app()->isProduction()) {
            $catalogCache = $this->cacheRepository(['catalog', 'catalog:teams']);
            $catalogVersion = (int) $catalogCache->get('api:v1:catalog:teams:version', 1);
            $cacheKey = sprintf('api:v1:catalog:teams:v%d:p%d:pg%d', $catalogVersion, $perPage, $page);
            $teams = $catalogCache->remember($cacheKey, 30, $buildPaginator);
        } else {
            $teams = $buildPaginator();
        }

        return PublicCatalogTeamResource::collection($teams)->response();
    }

    public function profiles(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 12), 1), 60);
        $page = max($request->integer('page', 1), 1);

        $buildPaginator = fn (): LengthAwarePaginator => User::query()
            ->where('is_profile_public', true)
            ->select(['id', 'nickname', 'role', 'description'])
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);

        if (app()->isProduction()) {
            $catalogCache = $this->cacheRepository(['catalog', 'catalog:profiles']);
            $catalogVersion = (int) $catalogCache->get('api:v1:catalog:profiles:version', 1);
            $cacheKey = sprintf('api:v1:catalog:profiles:v%d:p%d:pg%d', $catalogVersion, $perPage, $page);
            $profiles = $catalogCache->remember($cacheKey, 30, $buildPaginator);
        } else {
            $profiles = $buildPaginator();
        }

        return PublicCatalogProfileResource::collection($profiles)->response();
    }

    private function cacheRepository(array $tags): CacheRepository
    {
        if (Cache::supportsTags()) {
            return Cache::tags($tags);
        }

        return Cache::store();
    }
}
