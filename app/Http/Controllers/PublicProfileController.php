<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class PublicProfileController extends Controller
{
    public function show(User $user): View
    {
        abort_unless($user->is_profile_public, 404);

        $user->load([
            'teams' => fn ($query) => $query->latest()->limit(6),
            'hackatons' => fn ($query) => $query->latest()->limit(6),
            'judgeAssignments.hackaton',
            'certificates.hackaton',
            'teamRoles.role',
            'teamRoles.skills',
        ]);

        return view('pages.profile.public-show', [
            'profileUser' => $user,
        ]);
    }
}
