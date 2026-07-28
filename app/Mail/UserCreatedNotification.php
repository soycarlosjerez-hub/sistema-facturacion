<?php

namespace App\Mail;

use App\Mail\Middleware\ApplyGlobalSmtpConfig;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plainPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $this->user->load('businessInstance');
        return new Content(
            view: 'emails.user_created',
            with: [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->plainPassword,
                'instanceName' => $this->user->businessInstance?->nombre ?? config('app.name'),
            ],
        );
    }

    /**
     * Get the middleware applied to the mailable.
     */
    public function middleware(): array
    {
        return [new ApplyGlobalSmtpConfig()];
    }
}
