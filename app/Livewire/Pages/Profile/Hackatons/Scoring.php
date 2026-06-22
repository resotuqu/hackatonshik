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

        $stats = DB::table('hackaton_case_submissions as s')
            ->join('hackaton_cases as c', 'c.id', '=', 's.hackaton_case_id')
            ->leftJoin('hackaton_case_scores as sc', function ($join): void {
                $join->on('sc.hackaton_case_submission_id', '=', 's.id')
                    ->where('sc.is_final', '=', 1);
            })
            ->whereIn('c.hackaton_id', $ids)
            ->groupBy('c.hackaton_id')
            ->selectRaw('c.hackaton_id, COUNT(s.id) as total_cnt, SUM(CASE WHEN sc.id IS NOT NULL THEN 1 ELSE 0 END) as final_cnt')
            ->get()
            ->keyBy('hackaton_id');

        return $hackatons->map(function (Hackaton $hackaton) use ($stats): array {
            $row = $stats->get($hackaton->id);
            $total = (int) ($row?->total_cnt ?? 0);
            $final = (int) ($row?->final_cnt ?? 0);

            return [
                'hackaton' => $hackaton,
                'submissions_count' => $total,
                'final_scores_count' => $final,
                'submissions_without_final' => $total - $final,
            ];
        });
    }

    #[Layout('layouts::app', ['title' => 'Оценка работ'])]
    public function render(): View
    {
        return view('pages.profile.hackatons.scoring', [
            'scoringRows' => $this->scoringRows(),
        ]);
    }
}
