@props([
    'property' => 'avatar',
    'multiple' => false,
    'hint' => null,
    'outputSize' => 512,
    'quality' => 0.9,
])

@php
    $cropperConfig = [
        'property' => $property,
        'multiple' => (bool) $multiple,
        'outputSize' => (int) $outputSize,
        'quality' => (float) $quality,
    ];
@endphp

<div
    {{ $attributes->merge(['class' => 'w-full']) }}
    wire:ignore
    x-data="window.createAvatarCropperModal(@js($cropperConfig))"
>
    <input
        x-ref="fileInput"
        type="file"
        class="hidden"
        accept="image/*"
        @if ($multiple) multiple @endif
        @change="onFilesSelected($event)"
    />

    <div class="flex flex-col gap-2">
        <button type="button" class="btn btn-outline btn-secondary btn-sm w-fit" @click="pickFiles">
            Выбрать файл@if ($multiple)ы@endif
        </button>
        @if (filled($hint))
            <p class="text-sm text-base-content/70">{{ $hint }}</p>
        @endif
        <p x-show="state === 'error'" x-cloak class="text-sm text-error">
            Не удалось загрузить файл. Попробуйте ещё раз или выберите другое изображение.
        </p>
    </div>

    <dialog
        x-ref="dialog"
        class="modal"
        @close="onDialogClosed()"
    >
        <div class="modal-box max-w-3xl">
            <h3 class="text-lg font-bold">Обрезка аватара</h3>
            <p
                x-show="multiple && modalCaption"
                x-text="modalCaption"
                class="py-1 text-sm text-base-content/70"
            ></p>

            <div class="grid gap-4 py-4 md:grid-cols-[1fr_auto]">
                <div class="max-h-[min(70vh,28rem)] overflow-hidden rounded-xl border border-base-300 bg-base-200/30">
                    <img x-ref="cropImg" alt="" class="block max-w-full" />
                </div>
                <div class="flex flex-col items-center gap-2">
                    <span class="text-xs text-base-content/50">Предпросмотр</span>
                    <div class="avatar-crop-preview h-32 w-32 shrink-0 border border-base-300 bg-base-200">
                        <div x-ref="previewBox" class="h-full w-full overflow-hidden"></div>
                    </div>
                </div>
            </div>

            <p x-show="state === 'loading' || state === 'uploading'" x-cloak class="text-sm text-base-content/70">
                <span x-show="state === 'loading'">Загрузка редактора…</span>
                <span x-show="state === 'uploading'">Отправка на сервер…</span>
            </p>

            <div class="modal-action">
                <button type="button" class="btn btn-ghost" @click="cancelCrop" :disabled="state === 'uploading'">
                    Отмена
                </button>
                <button
                    type="button"
                    class="ui-cta-primary"
                    :disabled="state !== 'cropping'"
                    @click="applyCrop"
                >
                    Применить
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button type="submit" class="hidden">close</button>
        </form>
    </dialog>
</div>
