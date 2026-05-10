<div class="">
    <div wire:loading class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-busy="true" aria-label="Загрузка хакатонов">
        @foreach (range(1, 6) as $_)
            <x-profile-hackaton-card-skeleton />
        @endforeach
    </div>

    <div wire:loading.remove>
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/profile">Профиль</a></li>
            <li class="opacity-70">Мои хакатоны</li>
        </ul>
    </div>

    <h3 class="ui-heading-display mt-6 text-center text-2xl font-bold sm:text-3xl">Ваши хакатоны</h3>

    <x-mary-modal wire:model="deleteHackatonModal" title="Подтверждение удаления" class="backdrop-blur">
        Вы действительно хотите удалить хакатон? Это действие нельзя отменить.

        <x-slot:actions>
            <x-mary-button class="btn-error" label="Удалить" wire:click="deleteHackaton" />
            <x-mary-button label="Отмена" @click="$wire.deleteHackatonModal = false" />
        </x-slot:actions>
    </x-mary-modal>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        @forelse($this->hackatons as $hackaton)
            <x-marycard class="card card-border">
               <div class="overflow-hidden rounded-xl bg-base-200 aspect-video">
                        <img src="/uploads/{{$hackaton->image_url}}" class="w-full h-full object-cover" alt="{{$hackaton->title}}">
                    </div>
                <div class="mt-2 space-y-2">
                    <p class="card-title">{{$hackaton->title}}</p>
                    <x-mary-card class="card card-border bg-base-200">
                        <p>Принимает участие: {{$hackaton->participantsCount()}} команд</p>
                        <p>Даты проведения:
                            {{ $hackaton->start_at->format('d.m.Y H:i') }} — {{ $hackaton->end_at->format('d.m.Y H:i') }}</p>
                    </x-mary-card>
                </div>

                <x-slot:actions>
                    <a href="/hackatons/{{$hackaton->id}}">
                        <x-marybutton class="btn-ghost" label="Просмотреть" />
                    </a>
                    <x-marybutton class="btn-primary" label="Изменить" wire:click="editHackaton({{$hackaton->id}})" />
                    <x-marybutton class="btn-secondary" label="Участники" wire:click="participantsHackaton({{$hackaton->id}})" />
                    <x-marybutton class="btn-error" label="Удалить" wire:click="showDeleteHackatonModal({{$hackaton->id}})" />
                </x-slot:actions>

            </x-marycard>

        @empty
            <div class="col-span-full">
                <x-empty-state
                    title="Пока нет хакатонов"
                    description="Создайте событие для участников — настройте регистрацию, кейсы и судейство в одном месте."
                    icon="heroicons:rocket-launch"
                    action-href="/hackatons/create"
                    action-label="Создать хакатон"
                    secondary-action-href="/hackatons"
                    secondary-action-label="Каталог хакатонов"
                />
            </div>
        @endforelse
    </div>

    </div>
</div>
