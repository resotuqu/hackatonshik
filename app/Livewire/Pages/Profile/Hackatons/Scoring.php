<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Profile\Hackatons;

use App\Models\Hackaton;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Scoring extends Component
{
    public function mount(): void
    {
        abort_unless(Auth::user()?->isOrganizer(), 403);
    }

    /**
     * @return Collection<int, array{hackaton: Hackaton, submissions_count: int, final_scores_count: int, submissions_without_final: int}>
     */
    #[Computed]
    public function scoringRows(): Collection
    {
        $userId = Auth::id();
        $hackatons = Hackaton::query()
            ->where('user_id', $userId)
            ->orderByDesc('start_at')
            ->get();

        if ($hackatons->isEmpty()) {
            return collect();
        }

        $ids = $hackatons->pluck('id')->all();

        $submissionCounts = DB::table('hackaton_case_submissions as s')
            ->join('hackaton_cases as c', 'c.id', '=', 's.hackaton_case_id')
            ->whereIn('c.hackaton_id', $ids)
            ->groupBy('c.hackaton_id')
            ->selectRaw('c.hackaton_id as hackaton_id, COUNT(*) as cnt')
            ->pluck('cnt', 'hackaton_id');

        $finalScoreCounts = DB::table('hackaton_case_scores as sc')
            ->join('hackaton_case_submissions as s', 's.id', '=', 'sc.hackaton_case_submission_id')
            ->join('hackaton_cases as c', 'c.id', '=', 's.hackaton_case_id')
            ->whereIn('c.hackaton_id', $ids)
            ->where('sc.is_final', true)
            ->groupBy('c.hackaton_id')
            ->selectRaw('c.hackaton_id as hackaton_id, COUNT(*) as cnt')
            ->pluck('cnt', 'hackaton_id');

        $withoutFinalCounts = DB::table('hackaton_case_submissions as s')
            ->join('hackaton_cases as c', 'c.id', '=', 's.hackaton_case_id')
            ->whereIn('c.hackaton_id', $ids)
            ->whereNotExists(function ($q): void {
                $q->selectRaw('1')
                    ->from('hackaton_case_scores as sc')
                    ->whereColumn('sc.hackaton_case_submission_id', 's.id')
                    ->where('sc.is_final', true);
            })
            ->groupBy('c.hackaton_id')
            ->selectRaw('c.hackaton_id as hackaton_id, COUNT(*) as cnt')
            ->pluck('cnt', 'hackaton_id');

        return $hackatons->map(function (Hackaton $hackaton) use ($submissionCounts, $finalScoreCounts, $withoutFinalCounts): array {
            $hid = $hackaton->id;

            return [
                'hackaton' => $hackaton,
                'submissions_count' => (int) ($submissionCounts[$hid] ?? 0),
                'final_scores_count' => (int) ($finalScoreCounts[$hid] ?? 0),
                'submissions_without_final' => (int) ($withoutFinalCounts[$hid] ?? 0),
            ];
        });
    }

    #[Layout('layouts::app', ['title' => 'Оценка работ'])]
    public function render(): View
    {
        return view('pages.profile.hackatons.scoring', [
            'scoringRows' => $this->scoringRows,
        ]);
    }
}
