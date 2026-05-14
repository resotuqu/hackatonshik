<section id="hackaton-panel-documents" role="tabpanel" @class(['hidden' => ($hackatonTabFallback ?? 'description') !== 'documents']) data-tab-panel="hackaton" data-tab-value="documents">
    <div class="card bg-base-100 border border-base-200 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-xl">Документы хакатона</h2>
            @if ($hackaton->documents->isEmpty())
                <x-empty-state
                    embedded
                    title="Документов пока нет"
                    description="Проверяйте раздел позже или уточните детали у организатора."
                    icon="heroicons:document-text"
                />
            @else
                <div class="space-y-3">
                    @foreach ($hackaton->documents as $document)
                        <div class="rounded-xl border border-base-300 p-4">
                            <p class="font-semibold">{{ $document->name }}</p>
                            <x-safe-markdown :content="$document->description ?? ''" class="mt-1 text-base-content/80" />
                            <div class="mt-3">
                                <a class="btn btn-sm btn-outline"
                                    href="{{ asset('storage/' . $document->file_url) }}"
                                    target="_blank"
                                    rel="noopener noreferrer">
                                    Открыть документ
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
