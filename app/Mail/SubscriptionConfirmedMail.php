<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Company $company,
        public readonly string $plan,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Assinatura confirmada — Bem-vindo ao Invexa ' . ucfirst($this->plan) . '!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-confirmed',
        );
    }
}
