<?php

use App\Models\AvatarPreset;
use App\Models\AvatarPresetPack;
use App\Support\PresetAvatar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::app', ['title' => 'Аватарки (паки)'])]
class extends Component {
    use \Mary\Traits\Toast, WithFileUploads;

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
        foreach ($this->packs as $pack) {
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

        foreach ($pack->presets as $preset) {
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
        $slug = $preset->pack->slug;

        if (PresetAvatar::isPathUnderPack($preset->storage_path, $slug)) {
            Storage::disk('public')->delete($preset->storage_path);
        }

        $preset->delete();

        $this->success('Пресет удалён.', position: 'toast-center toast-top');
    }
};
?>

<div class="mx-auto w-full max-w-4xl space-y-6">
    <x-marytoast />

    <nav class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/admin">Админ-панель</a></li>
            <li class="opacity-70">Аватарки</li>
        </ul>
    </nav>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-display text-2xl font-semibold">Встроенные аватары (паки)</h1>
        <a href="/admin" class="btn btn-outline btn-sm">Назад в админку</a>
    </div>

    <section class="card border border-base-300 bg-base-100">
        <div class="card-body gap-4">
            <h2 class="card-title text-base">Новый пак</h2>
            <p class="text-sm text-base-content/70">Slug не меняется после создания. Папка на диске: <code class="text-xs">preset_avatars/packs/&lt;slug&gt;/</code></p>
            <div class="grid gap-4 md:grid-cols-3">
                <x-mary-input label="Название" wire:model="new_pack_name" placeholder="ВЕСНА 2026" />
                <x-mary-input label="Slug (латиница)" wire:model="new_pack_slug" placeholder="vesna-2026" />
                <x-mary-input label="Порядок сортировки" type="number" wire:model="new_pack_sort_order" />
            </div>
            <x-mary-button label="Создать пак" class="btn-primary" type="button" wire:click="createPack" />
        </div>
    </section>

    <section class="card border border-base-300 bg-base-100">
        <div class="card-body gap-4">
            <h2 class="card-title text-base">Загрузка в пак</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="label"><span class="label-text">Пак</span></label>
                    <select class="select select-bordered w-full" wire:model="upload_pack_id">
                        <option value="">— выберите —</option>
                        @foreach ($this->packs as $pack)
                            <option value="{{ $pack->id }}">{{ $pack->name }} ({{ $pack->slug }})</option>
                        @endforeach
                    </select>
                </div>
                <x-mary-input type="file" wire:model="upload_files" accept="image/*" multiple hint="Несколько файлов, до 3 МБ каждый" />
            </div>
            @error('upload_files')
                <p class="text-sm text-error">{{ $message }}</p>
            @enderror
            <x-mary-button label="Загрузить" class="btn-secondary" type="button" wire:click="uploadToPack" />
        </div>
    </section>

    @foreach ($this->packs as $pack)
        <section class="card border border-base-300 bg-base-100" wire:key="pack-card-{{ $pack->id }}">
            <div class="card-body gap-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="card-title text-base">{{ $pack->name }}</h2>
                        <p class="text-sm text-base-content/60">slug: <code>{{ $pack->slug }}</code></p>
                    </div>
                    <x-mary-button label="Удалить пак" class="btn-error btn-outline btn-sm" type="button" wire:click="deletePack({{ $pack->id }})" wire:confirm="Удалить пак и все аватарки в нём?" />
                </div>

                @isset($packForm[$pack->id])
                    <div class="grid gap-3 md:grid-cols-3">
                        <x-mary-input label="Название" wire:model="packForm.{{ $pack->id }}.name" />
                        <x-mary-input label="Порядок" type="number" wire:model="packForm.{{ $pack->id }}.sort_order" />
                        <x-marytoggle label="Активен" wire:model="packForm.{{ $pack->id }}.is_active" />
                    </div>
                    <x-mary-button label="Сохранить пак" class="btn-primary btn-sm" type="button" wire:click="updatePack({{ $pack->id }})" />
                @endisset

                <div class="divider text-sm">Файлы в паке</div>
                @if ($pack->presets->isEmpty())
                    <p class="text-sm text-base-content/70">Пока нет загруженных аватарок.</p>
                @else
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6">
                        @foreach ($pack->presets as $preset)
                            <div class="relative overflow-hidden rounded-xl border border-base-300" wire:key="preset-{{ $preset->id }}">
                                <img src="{{ asset('storage/'.$preset->storage_path) }}" alt="" class="aspect-square w-full object-cover" loading="lazy" />
                                <div class="absolute right-1 top-1">
                                    <button
                                        type="button"
                                        class="btn btn-circle btn-error btn-xs"
                                        title="Удалить"
                                        wire:click="deletePreset({{ $preset->id }})"
                                        wire:confirm="Удалить этот пресет?"
                                    >&times;</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endforeach

    @if ($this->packs->isEmpty())
        <p class="text-center text-base-content/70">Паков пока нет — создайте первый выше.</p>
    @endif
</div>
