<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use App\Models\HackatonCaseImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HackatonCaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Hackaton::query()->orderBy('id')->get() as $hackaton) {
            $slug = pathinfo((string) $hackaton->image_url, PATHINFO_FILENAME);
            $markdownPath = base_path("placeholders/{$slug}.md");
            if (! File::exists($markdownPath)) {
                continue;
            }

            $cases = $this->extractCases((string) File::get($markdownPath));
            if ($cases === []) {
                continue;
            }

            $hackaton->cases()->delete();
            $caseImages = $this->discoverCaseImages($slug);

            foreach ($cases as $order => $caseData) {
                $case = HackatonCase::query()->create([
                    'hackaton_id' => $hackaton->id,
                    'title' => $caseData['title'],
                    'description' => $caseData['description'],
                    'resources_json' => null,
                    'sort_order' => $order,
                    'is_published' => true,
                    'publish_at' => now()->subMinutes(10),
                ]);

                if ($slug === 'smartomega-gamejab-2026' && isset($caseImages[$order])) {
                    $storedPath = $this->storeCaseImage($slug, $caseImages[$order]);
                    HackatonCaseImage::query()->create([
                        'hackaton_case_id' => $case->id,
                        'path' => $storedPath,
                        'alt' => "Обложка кейса {$case->title}",
                        'sort_order' => 1,
                    ]);
                }

                foreach ($this->defaultFields() as $i => $field) {
                    HackatonCaseField::query()->create([
                        'hackaton_case_id' => $case->id,
                        'label' => $field['label'],
                        'key' => $field['key'].'_'.$case->id,
                        'type' => $field['type'],
                        'is_required' => $i < 2,
                        'sort_order' => $i,
                        'options_json' => null,
                    ]);
                }
            }
        }
    }

    /**
     * @return list<array{title: string, description: string}>
     */
    private function extractCases(string $markdown): array
    {
        preg_match_all('/^##\s+Кейс\s+\d+\.\s*(.+)$/mu', $markdown, $matches, PREG_OFFSET_CAPTURE);
        if (! isset($matches[1]) || $matches[1] === []) {
            return [];
        }

        $result = [];
        $total = count($matches[0]);
        for ($i = 0; $i < $total; $i++) {
            $fullHeader = $matches[0][$i][0];
            $title = trim((string) $matches[1][$i][0]);
            $sectionStart = (int) $matches[0][$i][1] + strlen($fullHeader);
            $sectionEnd = $i < $total - 1 ? (int) $matches[0][$i + 1][1] : strlen($markdown);

            $description = trim(substr($markdown, $sectionStart, $sectionEnd - $sectionStart));
            if ($description === '') {
                $description = 'Описание кейса будет опубликовано организатором.';
            }

            $result[] = [
                'title' => $title !== '' ? $title : 'Кейс',
                'description' => $description,
            ];
        }

        return $result;
    }

    /**
     * @return list<string>
     */
    private function discoverCaseImages(string $slug): array
    {
        $imagesPath = base_path("placeholders/{$slug}");
        if (! File::isDirectory($imagesPath)) {
            return [];
        }

        $imagePaths = array_values(array_filter(
            array_map(static fn ($file): string => $file->getRealPath() ?: '', File::files($imagesPath)),
            static fn (string $path): bool => $path !== '' && Str::endsWith(Str::lower($path), '.jpg'),
        ));

        sort($imagePaths);

        return $imagePaths;
    }

    /**
     * @return list<array{label: string, key: string, type: string}>
     */
    private function defaultFields(): array
    {
        return [
            ['label' => 'Название проекта', 'key' => 'solution_title', 'type' => HackatonCaseField::TYPE_TEXT],
            ['label' => 'Ссылка на репозиторий', 'key' => 'repo_url', 'type' => HackatonCaseField::TYPE_URL],
            ['label' => 'Описание решения', 'key' => 'solution_description', 'type' => HackatonCaseField::TYPE_TEXTAREA],
        ];
    }

    private function storeCaseImage(string $slug, string $sourcePath): string
    {
        $filename = pathinfo($sourcePath, PATHINFO_BASENAME);
        $targetPath = "case_photos/{$slug}/".Str::slug((string) pathinfo($filename, PATHINFO_FILENAME)).'.jpg';
        Storage::disk('public')->put($targetPath, (string) File::get($sourcePath));

        return $targetPath;
    }
}
