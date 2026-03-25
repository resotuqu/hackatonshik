<?php

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Responses\LoginResponse;
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
            session()->regenerate();
            $this->success('Успешная авторизация !', position: 'toast-center toast-top');
            return app(LoginResponse::class);
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
        <div>
            <script nonce="csp_nonce" src="https://unpkg.com/@vkid/sdk@<3.0.0/dist-sdk/umd/index.js"></script>
            <script nonce="csp_nonce" type="text/javascript">
                if ('VKIDSDK' in window) {
                    const VKID = window.VKIDSDK;

                    VKID.Config.init({
                        app: 54506609,
                        redirectUrl: 'https://hackatonshik.test/auth/vk/callback',
                        responseMode: VKID.ConfigResponseMode.Callback,
                        source: VKID.ConfigSource.LOWCODE,
                        scope: '', // Заполните нужными доступами по необходимости
                    });

                    const oneTap = new VKID.OneTap();

                    oneTap.render({
                        container: document.currentScript.parentElement,
                        showAlternativeLogin: true,
                        skin: 'secondary'
                    })
                        .on(VKID.WidgetEvents.ERROR, vkidOnError)
                        .on(VKID.OneTapInternalEvents.LOGIN_SUCCESS, function (payload) {
                            const code = payload.code;
                            const deviceId = payload.device_id;

                            VKID.Auth.exchangeCode(code, deviceId)
                                .then(vkidOnSuccess)
                                .catch(vkidOnError);
                        });

                    function vkidOnSuccess(data) {
                        // Обработка полученного результата
                    }

                    function vkidOnError(error) {
                        // Обработка ошибки
                    }
                }
            </script>
        </div>

    </x-maryform>
</div>
