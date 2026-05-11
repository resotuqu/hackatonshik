<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\HackatonLevel;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HackatonSeeder extends Seeder
{
    public function run(): void
    {
        $placeholders = $this->loadPlaceholders();
        if ($placeholders === []) {
            $this->command?->warn('Не найдены пары *.md + *.jpg в placeholders — пропуск HackatonSeeder.');

            return;
        }

        $partners = User::query()->where('role', 'partner')->orderBy('id')->get();
        if ($partners->count() < count($placeholders)) {
            $this->command?->warn('Недостаточно организаторов partner для сидирования хакатонов из placeholders.');

            return;
        }

        $now = Carbon::now()->startOfDay();
        $statusCycle = [
            HackatonStatus::REGISTRATION_OPEN,
            HackatonStatus::REGISTRATION_CLOSED,
            HackatonStatus::WAITING_START,
            HackatonStatus::CASES_ANNOUNCED,
            HackatonStatus::IN_PROGRESS,
            HackatonStatus::JUDGING,
            HackatonStatus::FINISHED,
            HackatonStatus::ARCHIVED,
        ];
        $levelCycle = [
            HackatonLevel::Beginner,
            HackatonLevel::Intermediate,
            HackatonLevel::Advanced,
            HackatonLevel::Pro,
        ];

        foreach ($placeholders as $index => $placeholder) {
            $status = $statusCycle[$index % count($statusCycle)];
            ['start_at' => $startAt, 'end_at' => $endAt, 'registration_deadline_at' => $deadlineAt] = $this->timelineForStatus($status, $now);
            $coverPath = $this->storeCover($placeholder['slug'], $placeholder['image_path']);

            Hackaton::query()->updateOrCreate(
                ['title' => $placeholder['title']],
                [
                    'user_id' => $partners[$index]->id,
                    'title' => $placeholder['title'],
                    'description' => $placeholder['description'],
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'is_public' => true,
                    'status' => $status,
                    'prize_fund' => 150000 + ($index * 100000),
                    'prize_places_count' => 3 + ($index % 3),
                    'level' => $levelCycle[$index % count($levelCycle)],
                    'registration_deadline_at' => $deadlineAt,
                    'image_url' => $coverPath,
                ],
            );
        }
    }

    /**
     * @return list<array{slug: string, title: string, description: string, image_path: string}>
     */
    private function loadPlaceholders(): array
    {
        $placeholdersPath = base_path('placeholders');
        if (! File::isDirectory($placeholdersPath)) {
            return [];
        }

        $result = [];
        foreach (File::files($placeholdersPath) as $file) {
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $slug = $file->getFilenameWithoutExtension();
            $imagePath = $placeholdersPath.DIRECTORY_SEPARATOR.$slug.'.jpg';
            if (! File::exists($imagePath)) {
                continue;
            }

            $markdown = trim((string) File::get($file->getRealPath()));
            [$title, $rawMarkdown] = $this->extractTitleAndDescription($slug, $markdown);
            $description = $this->sanitizePublicDescription($rawMarkdown, $title);

            $result[] = [
                'slug' => $slug,
                'title' => $title,
                'description' => $description,
                'image_path' => $imagePath,
            ];
        }

        usort($result, fn (array $a, array $b): int => $a['slug'] <=> $b['slug']);

        return $result;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function extractTitleAndDescription(string $slug, string $markdown): array
    {
        $lines = preg_split('/\R/u', $markdown) ?: [];
        $normalized = array_values(array_filter(array_map(
            static fn (string $line): string => trim($line),
            $lines,
        ), static fn (string $line): bool => $line !== ''));

        $title = Str::headline(str_replace('-', ' ', $slug));
        foreach ($normalized as $index => $line) {
            if (preg_match('/^#*\s*\*{0,2}\s*Название хакатона\s*:\s*\*{0,2}$/iu', $line) === 1) {
                $candidate = $normalized[$index + 1] ?? '';
                $candidate = trim((string) preg_replace('/^[#\*\-\s]+|[#\*]+$/u', '', $candidate));
                if ($candidate !== '') {
                    $title = $candidate;
                    break;
                }
            }
        }

        if ($title === Str::headline(str_replace('-', ' ', $slug))) {
            foreach ($normalized as $line) {
                if (preg_match('/^#+\s*(.+)$/u', $line, $matches) === 1) {
                    $title = trim((string) preg_replace('/[*_`]/u', '', $matches[1]));
                    break;
                }
            }
        }

        return [$title, $markdown];
    }

    private function sanitizePublicDescription(string $markdown, string $title): string
    {
        $md = trim($markdown);

        if (preg_match('/^##\s+Кейс\s+\d+\./m', $md, $matches, PREG_OFFSET_CAPTURE) === 1) {
            $md = trim(substr($md, 0, $matches[0][1]));
        }

        $md = $this->stripLeadingHackatonTitleBlock($md, $title);

        $md = preg_replace(
            '/^\*\*Красивое описание\s*\(для главной страницы и карточки\)\s*:\s*\*\*\s*$/miu',
            "## О хакатоне\n",
            $md,
            1
        ) ?? $md;

        $md = preg_replace('/^#\s+(?![#])/mu', '## ', $md, 1) ?? $md;

        $md = trim(preg_replace("/\n{3,}/u", "\n\n", $md) ?? $md);

        return $md !== '' ? $md : 'Описание уточняется организатором.';
    }

    private function stripLeadingHackatonTitleBlock(string $markdown, string $title): string
    {
        $lines = preg_split('/\R/u', $markdown) ?: [];
        $titleNorm = trim($title);
        $i = 0;
        $count = count($lines);

        while ($i < $count) {
            $trimmed = trim((string) $lines[$i]);
            if ($trimmed === '') {
                $i++;

                continue;
            }

            if (preg_match('/^#+\s*\*{0,2}\s*Название хакатона\s*:?\s*\*{0,2}\s*$/iu', $trimmed) === 1) {
                $i++;
                while ($i < $count && trim((string) $lines[$i]) === '') {
                    $i++;
                }
                if ($i < $count && trim((string) $lines[$i]) === $titleNorm) {
                    $i++;
                }

                continue;
            }

            if ($trimmed === $titleNorm) {
                $i++;

                continue;
            }

            break;
        }

        return trim(implode("\n", array_slice($lines, $i)));
    }

    private function storeCover(string $slug, string $sourceImagePath): string
    {
        $targetPath = "hackaton_photos/{$slug}.jpg";
        Storage::disk('public')->put($targetPath, (string) File::get($sourceImagePath));

        return $targetPath;
    }

    /**
     * @return array{start_at: Carbon, end_at: Carbon, registration_deadline_at: Carbon}
     */
    private function timelineForStatus(HackatonStatus $status, Carbon $now): array
    {
        return match ($status) {
            HackatonStatus::REGISTRATION_OPEN => [
                'start_at' => $now->copy()->addDays(20),
                'end_at' => $now->copy()->addDays(24),
                'registration_deadline_at' => $now->copy()->addDays(12),
            ],
            HackatonStatus::REGISTRATION_CLOSED => [
                'start_at' => $now->copy()->addDays(10),
                'end_at' => $now->copy()->addDays(13),
                'registration_deadline_at' => $now->copy()->subDay(),
            ],
            HackatonStatus::WAITING_START => [
                'start_at' => $now->copy()->addDay(),
                'end_at' => $now->copy()->addDays(4),
                'registration_deadline_at' => $now->copy()->subDays(5),
            ],
            HackatonStatus::CASES_ANNOUNCED => [
                'start_at' => $now->copy()->addDays(3),
                'end_at' => $now->copy()->addDays(6),
                'registration_deadline_at' => $now->copy()->subDays(4),
            ],
            HackatonStatus::IN_PROGRESS => [
                'start_at' => $now->copy()->subDay(),
                'end_at' => $now->copy()->addDays(2),
                'registration_deadline_at' => $now->copy()->subDays(7),
            ],
            HackatonStatus::JUDGING => [
                'start_at' => $now->copy()->subDays(8),
                'end_at' => $now->copy()->subDay(),
                'registration_deadline_at' => $now->copy()->subDays(14),
            ],
            HackatonStatus::ARCHIVED => [
                'start_at' => $now->copy()->subDays(120),
                'end_at' => $now->copy()->subDays(110),
                'registration_deadline_at' => $now->copy()->subDays(130),
            ],
            default => [
                'start_at' => $now->copy()->subDays(20),
                'end_at' => $now->copy()->subDays(10),
                'registration_deadline_at' => $now->copy()->subDays(30),
            ],
        };
    }
}
