<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\JudgeInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JudgeInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public JudgeInvitation $invitation,
        public string $acceptUrl,
    ) {
        $this->invitation->loadMissing(['hackaton', 'inviter']);
    }

    public function envelope(): Envelope
    {
        $hackatonTitle = $this->invitation->hackaton?->title ?? 'Хакатон';

        return new Envelope(
            subject: 'Приглашение судьёй — '.$hackatonTitle.' — Хакатонщик',
            to: [
                new Address($this->invitation->invited_email),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.judge-invitation',
        );
    }
}
