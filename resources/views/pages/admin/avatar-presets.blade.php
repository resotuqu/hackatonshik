<div class="mx-auto w-full max-w-4xl space-y-6">

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
            <x-mary-button label="Создать пак" class="btn-neutral" type="button" wire:click="createPack" />
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
                <x-avatar-cropper-modal property="upload_files" :multiple="true" hint="Несколько файлов, до 3 МБ каждый" />
            </div>
            @error('upload_files')
                <p class="text-sm text-error">{{ $message }}</p>
            @enderror
            <x-mary-button label="Загрузить" class="btn-outline" type="button" wire:click="uploadToPack" />
        </div>
    </section>

    @foreach ($this->packs as $pack)
        <section class="card border border-base-300 bg-base-100" wire:key="pack-card-{{ $pack->id }}">
            <div class="card-body gap-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="card-title text-base">{{ $pack->name }}</h2>
                        <p class="text-sm text-base-content/70">slug: <code>{{ $pack->slug }}</code></p>
                    </div>
                    <x-mary-button label="Удалить пак" class="btn-error btn-outline btn-sm" type="button" wire:click="deletePack({{ $pack->id }})" wire:confirm="Удалить пак и все аватарки в нём?" />
                </div>

                @isset($packForm[$pack->id])
                    <div class="grid gap-3 md:grid-cols-3">
                        <x-mary-input label="Название" wire:model="packForm.{{ $pack->id }}.name" />
                        <x-mary-input label="Порядок" type="number" wire:model="packForm.{{ $pack->id }}.sort_order" />
                        <x-marytoggle label="Активен" wire:model="packForm.{{ $pack->id }}.is_active" />
                    </div>
                    <x-mary-button label="Сохранить пак" class="btn-neutral btn-sm" type="button" wire:click="updatePack({{ $pack->id }})" />
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
