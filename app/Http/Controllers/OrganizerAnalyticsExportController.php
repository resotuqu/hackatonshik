<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Hackaton\BuildOrganizerFunnelMetrics;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrganizerAnalyticsExportController extends Controller
{
    public function __invoke(Request $request, BuildOrganizerFunnelMetrics $funnelMetrics): StreamedResponse
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($user->isOrganizer(), 403);

        $funnel = $funnelMetrics->handle($user);
        $filename = 'organizer_funnel_'.$user->id.'.csv';

        return response()->streamDownload(function () use ($funnel): void {
            $stream = fopen('php://output', 'wb');
            fputcsv($stream, [
                'hackaton_id',
                'title',
                'views',
                'applications',
                'accepted',
                'pending',
                'rejected',
                'conversion_rate_percent',
                'completion_rate_percent',
            ]);

            foreach ($funnel['hackatons'] as $row) {
                fputcsv($stream, [
                    $row['hackaton_id'],
                    $row['title'],
                    $row['views'],
                    $row['applications'],
                    $row['accepted'],
                    $row['pending'],
                    $row['rejected'],
                    $row['conversionRate'],
                    $row['completionRate'],
                ]);
            }

            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
