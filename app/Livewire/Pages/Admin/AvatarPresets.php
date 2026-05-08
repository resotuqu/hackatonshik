<?php

namespace App\Livewire\Pages\Admin;

use App\Models\AvatarPreset;
use App\Models\AvatarPresetPack;
use App\Support\PresetAvatar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Аватарки (паки)'])]
class AvatarPresets extends Component
{
    use Toast, WithFileUploads;

    public string $new_pack_name = '';

    public string $new_pack_slug = '';

    public int $new_pack_sort_order = 0;

    /** @var array<int, array{name: string, sort_order: int, is_active: bool}> */
    public array $packForm = [];

    public $upload_pack_id = null;

    public array $upload_files = [];

    public function mount(): void
    {
        $this->syncPackFormFromDatabase();
    }

    public function getPacksProperty(): Collection
    {
        return AvatarPresetPack::query()
            ->with(['presets' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function syncPackFormFromDatabase(): void
    {
        $this->packForm = [];
        $packs = AvatarPresetPack::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        foreach ($packs as $pack) {
            $this->packForm[$pack->id] = [
                'name' => $pack->name,
                'sort_order' => (int) $pack->sort_order,
                'is_active' => (bool) $pack->is_active,
            ];
        }
    }

    public function createPack(): void
    {
        $this->validate([
            'new_pack_name' => ['required', 'string', 'max:255'],
            'new_pack_slug' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:avatar_preset_packs,slug'],
            'new_pack_sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
        ], [
            'new_pack_name.required' => 'Укажите название пака.',
            'new_pack_slug.required' => 'Укажите slug (латиница и дефисы).',
            'new_pack_slug.regex' => 'Slug: только a-z, 0-9 и дефис.',
            'new_pack_slug.unique' => 'Такой slug уже занят.',
        ]);

        AvatarPresetPack::query()->create([
            'name' => $this->new_pack_name,
            'slug' => $this->new_pack_slug,
            'sort_order' => $this->new_pack_sort_order,
            'is_active' => true,
        ]);

        Storage::disk('public')->makeDirectory(PresetAvatar::packStorageDirectory($this->new_pack_slug));

        $this->reset(['new_pack_name', 'new_pack_slug', 'new_pack_sort_order']);
        $this->new_pack_sort_order = 0;
        $this->syncPackFormFromDatabase();
        $this->success('Пак создан.', position: 'toast-center toast-top');
    }

    public function updatePack(int $id): void
    {
        $this->validate([
            "packForm.$id.name" => ['required', 'string', 'max:255'],
            "packForm.$id.sort_order" => ['required', 'integer', 'min:0', 'max:999999'],
            "packForm.$id.is_active" => ['boolean'],
        ], [], [
            "packForm.$id.name" => 'название',
            "packForm.$id.sort_order" => 'порядок',
        ]);

        $pack = AvatarPresetPack::query()->findOrFail($id);
        $pack->update([
            'name' => $this->packForm[$id]['name'],
            'sort_order' => (int) $this->packForm[$id]['sort_order'],
            'is_active' => (bool) $this->packForm[$id]['is_active'],
        ]);

        $this->success('Пак обновлён.', position: 'toast-center toast-top');
    }

    public function deletePack(int $id): void
    {
        $pack = AvatarPresetPack::query()->with('presets')->findOrFail($id);

        foreach (AvatarPreset::query()->where('avatar_preset_pack_id', $pack->id)->get() as $preset) {
            if (PresetAvatar::isPathUnderPack($preset->storage_path, $pack->slug)) {
                Storage::disk('public')->delete($preset->storage_path);
            }
        }

        Storage::disk('public')->deleteDirectory(PresetAvatar::packStorageDirectory($pack->slug));

        $pack->delete();

        $this->syncPackFormFromDatabase();
        $this->success('Пак и файлы удалены.', position: 'toast-center toast-top');
    }

    public function uploadToPack(): void
    {
        $this->validate([
            'upload_pack_id' => ['required', 'exists:avatar_preset_packs,id'],
            'upload_files' => ['required', 'array', 'min:1'],
            'upload_files.*' => ['image', 'max:3072'],
        ], [
            'upload_pack_id.required' => 'Выберите пак.',
            'upload_files.required' => 'Выберите хотя бы один файл.',
            'upload_files.*.image' => 'Разрешены только изображения.',
            'upload_files.*.max' => 'Каждый файл до 3 МБ.',
        ]);

        $pack = AvatarPresetPack::query()->findOrFail($this->upload_pack_id);
        $dir = PresetAvatar::packStorageDirectory($pack->slug);
        Storage::disk('public')->makeDirectory($dir);

        $nextOrder = (int) (AvatarPreset::query()->where('avatar_preset_pack_id', $pack->id)->max('sort_order') ?? -1) + 1;

        foreach ($this->upload_files as $file) {
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            if (! in_array($ext, ['svg', 'png', 'jpg', 'jpeg', 'webp'], true)) {
                $this->addError('upload_files', 'Недопустимое расширение файла.');

                return;
            }
            $name = (string) Str::uuid().'.'.$ext;
            $path = $file->storeAs($dir, $name, 'public');

            AvatarPreset::query()->create([
                'avatar_preset_pack_id' => $pack->id,
                'storage_path' => $path,
                'sort_order' => $nextOrder,
            ]);
            $nextOrder++;
        }

        $this->reset(['upload_files', 'upload_pack_id']);
        $this->success('Файлы загружены.', position: 'toast-center toast-top');
    }

    public function deletePreset(int $id): void
    {
        $preset = AvatarPreset::query()->with('pack')->findOrFail($id);
        $pack = AvatarPresetPack::query()->find($preset->avatar_preset_pack_id);
        if (! $pack instanceof AvatarPresetPack) {
            return;
        }
        $slug = $pack->slug;

        if (PresetAvatar::isPathUnderPack($preset->storage_path, $slug)) {
            Storage::disk('public')->delete($preset->storage_path);
        }

        $preset->delete();

        $this->success('Пресет удалён.', position: 'toast-center toast-top');
    }

    public function render()
    {
        return view('pages.admin.avatar-presets');
    }
}
