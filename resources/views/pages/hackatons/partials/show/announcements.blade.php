<section id="hackaton-panel-announcements" role="tabpanel" class="hidden" data-tab-panel="hackaton" data-tab-value="announcements">
    <div class="card bg-base-100 border border-base-200 shadow-sm">
        <div class="card-body space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="card-title text-xl">Анонсы</h2>
                @if($isOrganizer)
                    <x-organizer-action-modal
                        :modal-id="$modals['announcement_create']"
                        button-label="Новый анонс"
                        title="Опубликовать анонс"
                        description="Подготовьте текст анонса и при необходимости запланируйте публикацию.">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('hackatons.announcements.store', $hackaton) }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="_open_modal" value="{{ $modals['announcement_create'] }}">
                            <input name="title" class="input input-bordered w-full" placeholder="Заголовок анонса" required autofocus>
                            <textarea name="body" class="textarea textarea-bordered w-full" rows="4" placeholder="Текст анонса" required></textarea>
                            <div class="space-y-2">
                                <label class="label p-0">
                                    <span class="label-text">Изображения анонса</span>
                                </label>
                                <input name="images[]" type="file" multiple accept="image/*" class="file-input file-input-bordered w-full">
                                <p class="text-xs text-base-content/70">Можно загрузить несколько изображений (до 10 файлов).</p>
                            </div>
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                                <select name="template_key" class="select select-bordered">
                                    <option value="">Без шаблона</option>
                                    @foreach($announcementTemplates as $templateKey => $templateName)
                                        <option value="{{ $templateKey }}">{{ $templateName }}</option>
                                    @endforeach
                                </select>
                                <input name="published_at" type="datetime-local" class="input input-bordered">
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input type="checkbox" name="is_draft" value="1" class="checkbox checkbox-sm">
                                    <span class="label-text">Сохранить как черновик</span>
                                </label>
                            </div>
                            <button class="btn btn-primary btn-sm">Сохранить анонс</button>
                        </form>
                    </x-organizer-action-modal>
                @endif
            </div>

            @if($hackaton->announcements->isEmpty())
                <x-empty-state
                    embedded
                    title="Анонсов пока нет"
                    description="Проверяйте раздел перед стартом и во время хакатона."
                    icon="heroicons:megaphone"
                />
            @else
                <div class="space-y-3">
                    @foreach($hackaton->announcements as $announcement)
                        <div class="rounded-xl border border-base-300 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="font-semibold">{{ $announcement->title }}</p>
                                    <p class="text-xs text-base-content/70">
                                        {{ $announcement->published_at?->format('d.m.Y H:i') ?? '—' }}
                                    </p>
                                    @if($isOrganizer && $announcement->is_draft)
                                        <span class="badge badge-warning badge-xs">Черновик</span>
                                    @elseif($isOrganizer && $announcement->published_at?->isFuture())
                                        <span class="badge badge-info badge-xs">Запланировано</span>
                                    @endif
                                </div>
                                @if($isOrganizer)
                                    <form method="POST" action="{{ route('hackatons.announcements.destroy', [$hackaton, $announcement]) }}"
                                        onsubmit="return confirm('Удалить анонс?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-xs btn-error">Удалить</button>
                                    </form>
                                @endif
                            </div>
                            <div class="markdown-body mt-2">
                                {!! \App\Support\SafeMarkdown::toHtml($announcement->body) !!}
                            </div>
                            @if ($announcement->images->isNotEmpty())
                                <div class="mt-3">
                                    <x-image-carousel
                                        :carousel-id="'announcement-carousel-'.$announcement->id"
                                        :items="$announcement->images"
                                        aspect-class="aspect-[16/9]"
                                        empty-text="Изображения отсутствуют" />
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
