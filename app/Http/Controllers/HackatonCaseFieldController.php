<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderHackatonCaseFieldsRequest;
use App\Http\Requests\StoreHackatonCaseFieldRequest;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class HackatonCaseFieldController extends Controller
{
    public function preview(Request $request, Hackaton $hackaton, HackatonCase $case): JsonResponse
    {
        abort_unless($case->hackaton_id === $hackaton->id, 404);
        Gate::authorize('update', $case);

        $validated = $request->validate([
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.type' => ['required', 'in:text,textarea,number,select,file'],
            'fields.*.is_required' => ['nullable', 'boolean'],
            'fields.*.conditional_on' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json([
            'preview' => collect($validated['fields'])->map(function (array $field): array {
                return [
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'required' => (bool) ($field['is_required'] ?? false),
                    'conditional_on' => $field['conditional_on'] ?? null,
                ];
            })->all(),
        ]);
    }

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
