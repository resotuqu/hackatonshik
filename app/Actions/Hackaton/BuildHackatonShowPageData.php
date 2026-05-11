<?php

declare(strict_types=1);

namespace App\Actions\Hackaton;

use App\Models\Hackaton;
use Illuminate\Http\Request;

/**
 * @deprecated Use ComposeHackatonShowPageData directly.
 */
final class BuildHackatonShowPageData
{
    public function __construct(
        private readonly ComposeHackatonShowPageData $composePageData,
        private readonly BuildHackatonShowPresentationData $buildPresentationData,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function build(Hackaton $hackaton, Request $request): array
    {
        $pageData = $this->composePageData->build($hackaton, $request);
        $presentationData = $this->buildPresentationData->build(
            $hackaton,
            $pageData['availableTeams'],
            $pageData['isOrganizer'],
            $pageData['isAssignedJudge'],
        );

        return array_merge($pageData, $presentationData);
    }
}
