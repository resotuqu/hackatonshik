<?php

namespace App\Http\Controllers\Profile;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportAccountDataController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $user->load([
            'skills',
            'teamRoles.team.hackaton',
            'caseSubmissions.hackatonCase.hackaton',
            'certificates.hackaton',
            'teamApplications' => fn ($q) => $q->where('status', ApplicationStatus::ACCEPTED)->with('teamRole.team.hackaton'),
        ]);

        $data = [
            'user' => $user,
            'generatedAt' => now(),
        ];

        $pdf = Pdf::loadView('pdf.account-data', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'account-data-'.str($user->nickname)->slug().'-'.now()->format('Y-m-d').'.pdf';

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties(['ip' => $request->ip()])
            ->log('exported_account_data');

        return $pdf->download($filename);
    }
}
