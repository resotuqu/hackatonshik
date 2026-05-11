<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\JudgeDomain;
use App\Http\Requests\AssignHackatonJudgeRequest;
use App\Http\Requests\StoreJudgeInvitationRequest;
use App\Mail\JudgeInvitationMail;
use App\Models\Hackaton;
use App\Models\JudgeInvitation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class JudgeManagementController extends Controller
{
    public function invite(StoreJudgeInvitationRequest $request, Hackaton $hackaton): RedirectResponse
    {
        $email = mb_strtolower($request->validated('email'));
        $invitedUser = User::query()->where('email', $email)->first();

        $invitation = JudgeInvitation::query()->create([
            'hackaton_id' => $hackaton->id,
            'invited_email' => $email,
            'invited_by' => $request->user()->id,
            'invited_user_id' => $invitedUser?->id,
            'token' => Str::random(64),
            'status' => JudgeInvitation::STATUS_PENDING,
        ]);

        $acceptUrl = route('judges.invitations.accept', ['token' => $invitation->token], absolute: true);
        Mail::to($email)->send(new JudgeInvitationMail($invitation, $acceptUrl));

        return back()->with('success', 'Инвайт судьи отправлен на email.');
    }

    public function assign(AssignHackatonJudgeRequest $request, Hackaton $hackaton): RedirectResponse
    {
        $judgeId = (int) $request->validated('user_id');
        $domain = (string) ($request->validated('domain') ?? JudgeDomain::DEV->value);

        $hackaton->judgeAssignments()->updateOrCreate(
            ['user_id' => $judgeId],
            [
                'assigned_by' => $request->user()->id,
                'assigned_at' => now(),
                'domain' => $domain,
            ],
        );

        return back()->with('success', 'Судья назначен на хакатон.');
    }

    public function unassign(Hackaton $hackaton, User $judge): RedirectResponse
    {
        abort_unless((int) $hackaton->user_id === (int) Auth::id(), 403);
        abort_unless($judge->isJudge(), 404);

        $hackaton->judgeAssignments()
            ->where('user_id', $judge->id)
            ->delete();

        return back()->with('success', 'Судья снят с хакатона.');
    }

    public function showAccept(string $token): View
    {
        $invitation = $this->resolvePendingInvitation($token);

        return view('pages.judges.accept-invitation', [
            'invitation' => $invitation,
            'hackaton' => $invitation->hackaton,
        ]);
    }

    public function accept(string $token): RedirectResponse
    {
        $invitation = $this->resolvePendingInvitation($token);

        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || mb_strtolower($user->email) !== $invitation->invited_email) {
            abort(403);
        }

        if (! $user->isJudge()) {
            $user->update(['role' => 'judge']);
        }

        $invitation->update([
            'invited_user_id' => $user->id,
            'status' => JudgeInvitation::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        $hackaton = Hackaton::query()->find($invitation->hackaton_id);
        if (! $hackaton instanceof Hackaton) {
            abort(404);
        }

        $hackaton->judgeAssignments()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'assigned_by' => $invitation->invited_by,
                'assigned_at' => now(),
            ],
        );

        return redirect()
            ->route('hackatons.show', $hackaton)
            ->with('success', 'Вы подтверждены как судья и назначены на хакатон.');
    }

    private function resolvePendingInvitation(string $token): JudgeInvitation
    {
        /** @var JudgeInvitation $invitation */
        $invitation = JudgeInvitation::query()
            ->with('hackaton:id,title')
            ->where('token', $token)
            ->where('status', JudgeInvitation::STATUS_PENDING)
            ->firstOrFail();

        return $invitation;
    }
}
