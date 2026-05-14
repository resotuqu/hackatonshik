<div>
    <div wire:loading class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-busy="true" aria-label="Загрузка хакатонов">
        @foreach (range(1, 6) as $_)
            <x-profile-hackaton-card-skeleton />
        @endforeach
    </div>

    <div wire:loading.remove class="space-y-8">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="/">Главная</a></li>
                <li><a href="{{ route('profile') }}">Профиль</a></li>
                <li class="opacity-70">Организатор</li>
            </ul>
        </div>

        <x-mary-modal wire:model="deleteHackatonModal" title="Подтверждение удаления" class="backdrop-blur">
            Вы действительно хотите удалить хакатон? Это действие нельзя отменить.

            <x-slot:actions>
                <x-mary-button class="btn-error" label="Удалить" wire:click="deleteHackaton" />
                <x-mary-button label="Отмена" @click="$wire.deleteHackatonModal = false" />
            </x-slot:actions>
        </x-mary-modal>

        @include('pages.profile.hackatons.hub-body', ['showGlobalPendingStrip' => true])
    </div>
</div>
