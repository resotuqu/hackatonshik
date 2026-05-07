<div>
    <x-marytoast />

    <x-maryform wire:submit="save" class="justify-self-center w-full lg:w-1/2">
        <x-mary-header title="Вход в админ-панель" separator />

        <x-mary-input label="Логин" wire:model="login" placeholder="Admin" />
        <x-marypassword label="Пароль" wire:model="password" />

        <x-slot:actions>
            <x-mary-button class="btn-primary" label="Войти" type="submit" />
        </x-slot:actions>
    </x-maryform>
</div>
