<?php

use App\Livewire\DocumentDownload;
use App\Models\HackatonDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('document download component renders document title', function () {
    $document = HackatonDocument::factory()->create([
        'name' => 'Уникальное имя документа теста',
    ]);

    Livewire::test(DocumentDownload::class, ['hackatonDocument' => $document])
        ->assertSee('Уникальное имя документа теста')
        ->assertSee('Скачать');
});

test('document download component renders description markdown safely', function () {
    $document = HackatonDocument::factory()->create([
        'name' => 'DocLivewireMdTitle',
        'description' => 'Текст **выделение** конец',
    ]);

    Livewire::test(DocumentDownload::class, ['hackatonDocument' => $document])
        ->assertSeeHtml('<strong>выделение</strong>');
});
