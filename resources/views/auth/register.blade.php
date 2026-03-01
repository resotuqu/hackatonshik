<x-layout>

    <x-slot:title>Регистрация</x-slot:title>

    <div>

        <div class="w-full flex justify-center">
            <form action="/register" method="post" class="bg-slate-700 w-1/2 rounded-sm  py-4 px-6">
                <h3 class="text-2xl text-center text-white">Регистрация</h3>
                <div class="flex flex-col">
                    @csrf
                    <x-form-input name="fio" type="text" label="Фамилия Имя Отчество" />
                    <x-form-input name="date_of_birth" type="date" label="Дата рождения" />
                    <x-form-input name="email" type="email" label="Адрес электронной почты" />
                    <x-form-input name="nickname" type="text" label="Псевдоним (например, vova_vlad_123)" />
                    <x-form-input name="password" type="password" label="Пароль" />
                    <x-form-input name="password_confirmation" type="password" label="Подтверждение пароля" />
                    <x-form-input name="phone" type="phone" label="Контактный номер телефона" />
                    <button class="py-2 px-4 bg-blue-400 text-white mt-6 rounded-sm cursor-pointer" type="submit">Зарегистрироваться</button>
                </div>
            </form>
        </div>

    </div>

</x-layout>
