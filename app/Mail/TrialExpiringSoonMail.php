<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialExpiringSoonMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $trialEndsAt) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏳ Seu período de teste no Invexa está acabando',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.trial-expiring',
            with: [
                'user'        => $this->user,
                'trialEndsAt' => $this->trialEndsAt,
            ],
        );
    }
}
