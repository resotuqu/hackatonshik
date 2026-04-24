<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Сертификаты'])]
class extends Component {
    #[Computed]
    public function certificates()
    {
        return Auth::user()?->certificates()->with('hackaton')->latest('issued_at')->get() ?? collect();
    }
};
?>

<div class="mx-auto w-full max-w-5xl space-y-4">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/profile">Профиль</a></li>
            <li class="opacity-70">Сертификаты</li>
        </ul>
    </div>

    <x-mary-card title="Мои сертификаты" class="card card-border bg-base-100">
        @if($this->certificates->isEmpty())
            <p class="text-base-content/70">Сертификаты пока не выданы.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Хакатон</th>
                            <th>Название</th>
                            <th>Дата выдачи</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->certificates as $certificate)
                            <tr>
                                <td>{{ $certificate->hackaton->title }}</td>
                                <td>{{ $certificate->title }}</td>
                                <td>{{ $certificate->issued_at?->format('d.m.Y') ?? '—' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-sm btn-outline">
                                        Скачать
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-mary-card>
</div>
