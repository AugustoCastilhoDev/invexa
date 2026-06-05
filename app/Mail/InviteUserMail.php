<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $invitedUser,
        public User $invitedBy,
        public string $inviteUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Você foi convidado para o ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invite',
        );
    }
}
