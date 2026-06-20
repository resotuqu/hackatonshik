<div>

    <div class="text-sm breadcrumbs mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}">Админ</a></li>
            <li class="opacity-70">Новости</li>
        </ul>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Управление новостями</h1>
        <x-mary-button label="Создать" wire:click="create" class="btn-neutral btn-sm" />
    </div>

    @if($showForm)
        <x-mary-card class="card border border-base-300 bg-base-100 mb-6">
            <x-maryform wire:submit="save" class="space-y-4">
                <x-mary-input label="Заголовок" wire:model="title" />
                <x-mary-input label="Slug" wire:model="slug" hint="URL-идентификатор" />
                <label class="form-control w-full">
                    <div class="label"><span class="label-text">Краткое описание</span></div>
                    <textarea class="textarea textarea-bordered w-full" wire:model="excerpt" rows="2"></textarea>
                </label>
                <label class="form-control w-full">
                    <div class="label"><span class="label-text">Текст (markdown)</span></div>
                    <textarea class="textarea textarea-bordered w-full" wire:model="body" rows="8"></textarea>
                </label>
                <x-marydatetime label="Дата публикации" wire:model="published_at" />
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" class="checkbox" wire:model="is_published" />
                    <span class="label-text">Опубликовано</span>
                </label>
                <div class="flex gap-2">
                    <x-mary-button type="submit" label="Сохранить" class="btn-neutral" />
                    <x-mary-button label="Отмена" wire:click="cancel" class="btn-ghost" />
                </div>
            </x-maryform>
        </x-mary-card>
    @endif

    <x-mary-card class="card border border-base-300 bg-base-100">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Заголовок</th>
                        <th>Slug</th>
                        <th>Опубликовано</th>
                        <th>Дата</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                        <tr>
                            <td>{{ $post->title }}</td>
                            <td class="text-sm opacity-70">{{ $post->slug }}</td>
                            <td>
                                @if($post->is_published)
                                    <span class="badge badge-success">Да</span>
                                @else
                                    <span class="badge badge-ghost">Нет</span>
                                @endif
                            </td>
                            <td class="text-sm">{{ $post->published_at?->format('d.m.Y H:i') ?? '—' }}</td>
                            <td class="text-right space-x-1">
                                <x-mary-button wire:click="edit({{ $post->id }})" class="btn-xs btn-outline" label="Изменить" />
                                <x-mary-button
                                    wire:click="delete({{ $post->id }})"
                                    wire:confirm="Удалить новость?"
                                    class="btn-xs btn-error"
                                    label="Удалить"
                                />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $posts->links() }}</div>
    </x-mary-card>
</div>
