<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderHackatonCaseFieldsRequest;
use App\Http\Requests\StoreHackatonCaseFieldRequest;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class HackatonCaseFieldController extends Controller
{
    public function store(StoreHackatonCaseFieldRequest $request, Hackaton $hackaton, HackatonCase $case): RedirectResponse
    {
        abort_unless($case->hackaton_id === $hackaton->id, 404);
        Gate::authorize('update', $case);

        $validated = $request->validated();
        $nextOrder = (int) $case->fields()->max('sort_order') + 1;

        $baseKey = Str::slug($validated['label'], '_');
        if ($baseKey === '') {
            $baseKey = 'field';
        }

        $key = $baseKey;
        $suffix = 2;
        while ($case->fields()->where('key', $key)->exists()) {
            $key = "{$baseKey}_{$suffix}";
            $suffix++;
        }

        $case->fields()->create([
            'label' => $validated['label'],
            'key' => $key,
            'type' => $validated['type'],
            'is_required' => (bool) ($validated['is_required'] ?? false),
            'sort_order' => $nextOrder,
        ]);

        return back()->with('success', 'Поле кейса добавлено.');
    }

    public function destroy(Hackaton $hackaton, HackatonCase $case, HackatonCaseField $field): RedirectResponse
    {
        abort_unless($case->hackaton_id === $hackaton->id, 404);
        abort_unless($field->hackaton_case_id === $case->id, 404);
        Gate::authorize('update', $case);

        $field->delete();

        return back()->with('success', 'Поле кейса удалено.');
    }

    public function reorder(ReorderHackatonCaseFieldsRequest $request, Hackaton $hackaton, HackatonCase $case): RedirectResponse
    {
        abort_unless($case->hackaton_id === $hackaton->id, 404);
        Gate::authorize('update', $case);

        $fieldIds = $request->validated('field_ids');
        $validIds = $case->fields()->whereIn('id', $fieldIds)->pluck('id')->map(fn ($id) => (int) $id)->all();

        if (count($validIds) !== count($fieldIds) || count($validIds) !== $case->fields()->count()) {
            return back()->with('error', 'Неверный набор полей для сортировки.');
        }

        DB::transaction(function () use ($fieldIds, $case): void {
            foreach (array_values($fieldIds) as $index => $fieldId) {
                HackatonCaseField::query()
                    ->where('hackaton_case_id', $case->id)
                    ->where('id', $fieldId)
                    ->update(['sort_order' => $index]);
            }
        });

        return back()->with('success', 'Порядок полей обновлён.');
    }
}
