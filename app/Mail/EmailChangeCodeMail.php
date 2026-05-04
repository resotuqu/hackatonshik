<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChangeCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code,
        public string $mailSubject,
        public string $intro,
        public string $disclaimer,
        public ?string $recipientEmail = null,
    ) {}

    public function envelope(): Envelope
    {
        $email = $this->recipientEmail ?? $this->user->getEmailForVerification();
        $name = filled($this->user->fio) ? $this->user->fio : null;

        return new Envelope(
            subject: $this->mailSubject,
            to: [new Address($email, $name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.email-change-code',
        );
    }
}
