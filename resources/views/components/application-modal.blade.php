@props([
    'type' => 'team', // 'team' или 'hackaton'
    'id' => null,     // team_role_id или hackaton_id
    'title' => 'Подать заявку',
    'action' => '',
    'teams' => collect(),
    'triggerLabel' => 'Подать заявку',
    'triggerClass' => 'btn btn-primary btn-sm',
])

@php
    $modalId = 'application-modal-' . $type . '-' . $id . '-' . uniqid();
@endphp

<div class="inline-block">
    <label for="{{ $modalId }}" class="{{ $triggerClass }}">{{ $triggerLabel }}</label>
    <input type="checkbox" id="{{ $modalId }}" class="modal-toggle" />

    <div class="modal modal-bottom sm:modal-middle" role="dialog">
        <div class="modal-box max-w-xl">
            <h3 class="font-bold text-lg">{{ $title }}</h3>

            <form 
                method="POST" 
                action="{{ $action }}"
                onsubmit="this.querySelector('button[type=submit]')?.setAttribute('disabled', 'disabled')"
                class="mt-4 space-y-4">
                @csrf

                @if($type === 'team')
                    <input type="hidden" name="team_role_id" value="{{ $id }}">
                @else
                    <input type="hidden" name="hackaton_id" value="{{ $id }}">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Команда</span>
                        </label>
                        <select name="team_id" class="select select-bordered w-full" required>
                            <option value="" disabled selected>Выберите команду</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->title }}</option>
                            @endforeach
                        </select>
                        @if ($teams->isEmpty())
                            <p class="label-text-alt text-error mt-2">У вас нет команд для подачи заявки.</p>
                        @endif
                    </div>
                @endif

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Сообщение (необязательно)</span>
                    </label>
                    <textarea 
                        name="message" 
                        class="textarea textarea-bordered h-24" 
                        placeholder="Расскажите, почему хотите вступить..."></textarea>
                    <p class="label-text-alt mt-2">
                        После отправки заявки вы сможете отслеживать ее статус на странице объявления.
                    </p>
                </div>

                <div class="modal-action flex-col-reverse gap-2 sm:flex-row">
                    <label for="{{ $modalId }}" class="btn btn-ghost w-full sm:w-auto">Отмена</label>
                    <button type="submit" 
                            @disabled($type === 'hackaton' && $teams->isEmpty())
                            class="btn btn-primary w-full sm:w-auto">Отправить заявку</button>
                </div>
            </form>
        </div>

        <label class="modal-backdrop" for="{{ $modalId }}">close</label>
    </div>
</div>