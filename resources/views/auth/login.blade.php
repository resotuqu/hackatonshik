<x-layout>

    <x-slot:title>Авторизация</x-slot:title>

    <div>

        <div class="w-full flex justify-center">
            <form action="/login" method="post" class="bg-slate-700 w-1/2 rounded-sm  py-4 px-6">
                <h3 class="text-2xl text-center text-white">Авторизация</h3>
                <div class="flex flex-col">
                    @csrf
                    <x-form-input name="email" type="email" label="Адрес электронной почты" />
                    <x-form-input name="password" type="password" label="Пароль" />
                    <button class="py-2 px-4 bg-blue-400 text-white mt-6 rounded-sm cursor-pointer" type="submit">Авторизироваться</button>
                </div>
            </form>
        </div>

    </div>

</x-layout>
