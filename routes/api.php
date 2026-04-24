<?php

declare(strict_types=1);

use App\Http\Controllers\Api\PublicCatalogController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/hackatons', [PublicCatalogController::class, 'hackatons']);
    Route::get('/teams', [PublicCatalogController::class, 'teams']);
    Route::get('/profiles', [PublicCatalogController::class, 'profiles']);
});
