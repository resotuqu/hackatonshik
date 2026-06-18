
<div class="mx-auto w-full max-w-5xl">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
        {{-- Left info panel — changes based on selected account type --}}
        <section class="card h-fit self-start border border-base-200 bg-base-100 shadow-sm lg:col-span-2">
            <div class="card-body justify-start space-y-4">
                @if($accountType === 'partner')
                    <div class="inline-flex items-center gap-2">
                        <span class="badge badge-secondary badge-sm">Организатор</span>
                    </div>
                    <h2 class="text-2xl font-semibold leading-tight">Проводите хакатоны на Хакатонщике</h2>
                    <p class="text-sm text-base-content/70">
                        Создайте аккаунт организатора, чтобы публиковать хакатоны, принимать заявки команд и управлять соревнованиями.
                    </p>
                    <div class="grid gap-2 text-sm">
                        <div class="rounded-lg border border-base-300 bg-base-200 p-3">
                            Создавайте и публикуйте хакатоны с призовым фондом
                        </div>
                        <div class="rounded-lg border border-base-300 bg-base-200 p-3">
                            Принимайте и отклоняйте заявки команд
                        </div>
                        <div class="rounded-lg border border-base-300 bg-base-200 p-3">
                            Добавляйте кейсы, судей и выдавайте сертификаты
                        </div>
                    </div>
                @else
                    <h2 class="text-2xl font-semibold leading-tight">Добро пожаловать в Хакатонщик</h2>
                    <p class="text-sm text-base-content/70">
                        Создайте аккаунт, чтобы участвовать в хакатонах, собирать команды и отправлять решения кейсов.
                    </p>
                    <div class="grid gap-2 text-sm">
                        <div class="rounded-lg border border-base-300 bg-base-200 p-3">
                            Создавайте профиль участника и вступайте в команды
                        </div>
                        <div class="rounded-lg border border-base-300 bg-base-200 p-3">
                            Подавайте заявки на хакатоны и отслеживайте статусы
                        </div>
                        <div class="rounded-lg border border-base-300 bg-base-200 p-3">
                            Получайте анонсы и сертификаты в личном кабинете
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <x-maryform
            wire:submit.prevent="{{ $step < 4 ? 'nextStep' : 'save' }}"
            class="card border border-base-200 bg-base-100 p-4 shadow-sm sm:p-6 lg:col-span-3"
        >
            @php
                $progressPercent = (int) round(($step / 4) * 100);
            @endphp
            <x-mary-header title="Регистрация" separator />

            <ul class="steps steps-horizontal mb-6 w-full max-w-full flex-wrap justify-start gap-y-2 text-[0.65rem] sm:text-xs">
                <li class="step {{ $step >= 1 ? 'step-primary' : '' }}">Личные данные</li>
                <li class="step {{ $step >= 2 ? 'step-primary' : '' }}">Аккаунт</li>
                <li class="step {{ $step >= 3 ? 'step-primary' : '' }}">Пароль</li>
                <li class="step {{ $step >= 4 ? 'step-primary' : '' }}">Телефон</li>
            </ul>
            <div class="mb-6">
                <div class="mb-1 flex items-center justify-between text-xs text-base-content/70">
                    <span>Прогресс регистрации</span>
                    <span class="tabular-nums">{{ $progressPercent }}%</span>
                </div>
                <progress class="progress progress-primary h-2 w-full" value="{{ $progressPercent }}" max="100"></progress>
            </div>

            @if ($step === 1)
                {{-- Account type picker --}}
                <div class="mb-4">
                    <p class="mb-2 text-sm font-medium text-base-content/80">Тип аккаунта</p>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="accountType" value="user" class="peer sr-only" />
                            <div class="flex flex-col items-center gap-1.5 rounded-xl border-2 px-3 py-3 text-center transition
                                peer-checked:border-primary peer-checked:bg-primary/5
                                border-base-300 bg-base-200/40 hover:border-base-400">
                                <x-app-icon icon="heroicons:user" class="h-6 w-6 text-base-content/60 peer-checked:text-primary" />
                                <span class="text-sm font-semibold">Участник</span>
                                <span class="text-[11px] text-base-content/60">Участвую в хакатонах</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="accountType" value="partner" class="peer sr-only" />
                            <div class="flex flex-col items-center gap-1.5 rounded-xl border-2 px-3 py-3 text-center transition
                                peer-checked:border-secondary peer-checked:bg-secondary/5
                                border-base-300 bg-base-200/40 hover:border-base-400">
                                <x-app-icon icon="heroicons:building-office-2" class="h-6 w-6 text-base-content/60" />
                                <span class="text-sm font-semibold">Организатор</span>
                                <span class="text-[11px] text-base-content/60">Провожу хакатоны</span>
                            </div>
                        </label>
                    </div>
                    @error('accountType')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-xs text-base-content/60 mb-2 rounded-lg border border-base-300 bg-base-200/40 px-3 py-2">
                    Если вы закроете страницу до завершения регистрации, данные не сохранятся — позже нужно будет заполнить форму заново.
                </p>
                <x-mary-input label="Фамилия, Имя, Отчество" wire:model="fio" placeholder="Владимир" hint="Введите ваше фио" />
                <x-marydatetime label="Дата рождения" hint="Введите вашу дату рождения" wire:model="date_of_birth" />
            @endif

            @if ($step === 2)
                <x-mary-input label="Адрес электронной почты" wire:model="email" placeholder="example@mail.com"
                    hint="Введите вашу электронную почту" />
                <x-mary-input label="Псевдоним" wire:model="nickname" placeholder="vova_vlad_123" hint="Введите ваш псевдоним" />
            @endif

            @if ($step === 3)
                <div x-data="{ password: @entangle('password').live }" class="space-y-2">
                    <x-marypassword label="Пароль" wire:model="password" />
                    <div class="space-y-1">
                        <div class="h-2 w-full rounded-full bg-base-300">
                            <div
                                class="h-2 rounded-full transition-all"
                                :class="password.length >= 12 ? 'bg-success' : (password.length >= 8 ? 'bg-warning' : 'bg-error')"
                                :style="`width: ${Math.min(100, Math.max(15, password.length * 8))}%`"
                            ></div>
                        </div>
                        <p class="text-xs text-base-content/70" x-text="password.length >= 12 ? 'Сильный пароль' : (password.length >= 8 ? 'Средний пароль' : 'Слабый пароль')"></p>
                    </div>
                </div>
                <x-marypassword label="Подтверждение пароля" wire:model="password_confirmation" />
            @endif

            @if ($step === 4)
                <x-mary-input label="Контактный номер телефона" wire:model="phone" prefix="+" />
            @endif

            <x-slot:actions class="flex w-full flex-col gap-2 sm:flex-row sm:justify-end">
                @if ($step > 1)
                    <x-marybutton class="btn-outline w-full sm:w-auto" label="Назад" type="button" wire:click="previousStep" />
                @endif
                @if ($step < 4)
                    <x-marybutton class="btn-primary w-full sm:min-w-40" label="Далее" type="submit" />
                @else
                    <x-marybutton class="btn-primary w-full sm:min-w-40" label="Зарегистрироваться" type="submit" />
                @endif
            </x-slot:actions>
            <div class="mt-2 grid gap-2">
                <a href="/auth/yandex/redirect" class="block w-full rounded-xl bg-[#FC3F1D] px-4 py-3 text-white transition hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-[#FC3F1D]/40">
                    <span class="inline-flex w-full items-center justify-center gap-3 text-sm font-semibold">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white text-sm font-black text-[#FC3F1D]">Я</span>
                        Войти или зарегистрироваться через Яндекс
                    </span>
                </a>
                <a href="/auth/vk/redirect" class="btn btn-outline w-full">
                    Войти или зарегистрироваться через VK
                </a>
            </div>
        </x-maryform>
    </div>
</div>
