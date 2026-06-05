<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialEndingMail extends Mailable
{
    use Queueable, SerializesModels;

    public int $daysLeft;

    public function __construct(public User $user)
    {
        $this->daysLeft = $user->company?->trialDaysLeft() ?? 0;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->daysLeft <= 1
                ? '⚠️ Seu trial no Invexa encerra hoje!'
                : "⏳ Seu trial no Invexa encerra em {$this->daysLeft} dias"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.trial-ending');
    }
}
