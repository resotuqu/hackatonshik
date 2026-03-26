<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;
new #[Layout('layouts::app', ['title' => 'Авторизация'])]
class extends Component {


    use \Mary\Traits\Toast;

    #[Validate(['email' => ['required', 'email']])]
    public $email = '';
    #[Validate(['password' => 'required'])]
    public $password = '';
    public $remember = false;


    public function save()
    {
        try {   
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Ошибка авторизации !', position: 'toast-center toast-top');
            throw $e;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->success('Успешная авторизация !', position: 'toast-center toast-top');
            session()->regenerate();

            return $this->redirect('/');
        }

        $this->error('Ошибка авторизации !', position: 'toast-center toast-top');
        $this->addError('email', __('auth.failed'));
    }

};
?>
<div>
    <x-marytoast />
    <x-maryform wire:submit="save" class="justify-self-center w-full lg:w-1/2">
        <x-mary-header title="Авторизация" separator/>
        <x-mary-input label="Адрес электронной почты" wire:model="email" placeholder="example@mail.com" hint="Введите вашу электронную почту"/>
        <x-marypassword label="Пароль" wire:model="password"/>
        <x-marytoggle label="Запомнить меня" wire:model="remember"/>
        <x-slot:actions class="w-full">
            <x-mary-button class="btn-primary" label="Авторизироваться" type="submit"/>
        </x-slot:actions>

        <a href="/auth/yandex/redirect" class="block w-full">
            <x-mary-button class="btn-neutral w-full" label="Войти через Яндекс ID" />
        </a>
        
        <div>
            <script nonce="csp_nonce" src="https://unpkg.com/@vkid/sdk@<3.0.0/dist-sdk/umd/index.js"></script>
            <script nonce="csp_nonce" type="text/javascript">
                if ('VKIDSDK' in window) {
                    const VKID = window.VKIDSDK;

                    VKID.Config.init({
                        app: 54507553,
                        redirectUrl: 'https://hackatonshik.test/auth/vk/callback',
                        responseMode: VKID.ConfigResponseMode.Callback,
                        source: VKID.ConfigSource.LOWCODE,
                        scope: '', // Заполните нужными доступами по необходимости
                    });

                    const oAuth = new VKID.OAuthList();

                    oAuth.render({
                        container: document.currentScript.parentElement,
                        oauthList: [
                            'vkid'
                        ]
                    })
                        .on(VKID.WidgetEvents.ERROR, vkidOnError)
                        .on(VKID.OAuthListInternalEvents.LOGIN_SUCCESS, function (payload) {
                            const code = payload.code;
                            const deviceId = payload.device_id;
                            const callbackUrl = `/auth/vk/callback?code=${encodeURIComponent(code)}&device_id=${encodeURIComponent(deviceId)}`;

                            window.location.href = callbackUrl;
                        });

                    function vkidOnError(error) {
                        // Обработка ошибки
                    }
                }
            </script>
        </div>

    </x-maryform>
</div>
