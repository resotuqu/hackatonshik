<?php

namespace App\Livewire\Pages\Contacts;

use App\Models\ContactMessage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Контакты'])]
class Index extends Component
{
    use Toast;

    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public string $telegram = '';

    public function send(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'telegram' => ['nullable', 'string', 'max:80'],
        ]);

        ContactMessage::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'telegram' => $this->telegram !== '' ? $this->telegram : null,
            'ip_address' => request()->ip(),
        ]);

        $this->reset(['name', 'email', 'subject', 'message', 'telegram']);
        $this->success('Спасибо! Мы получили ваше сообщение и скоро свяжемся с вами.', position: 'toast-center toast-top');
    }

    public function render()
    {
        return view('pages.contacts.index');
    }
}
