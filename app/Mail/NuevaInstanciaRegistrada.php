<?php

namespace App\Mail;

use App\Mail\Middleware\ApplyGlobalSmtpConfig;
use App\Models\BusinessInstance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaInstanciaRegistrada extends Mailable
{
    use Queueable, SerializesModels;

    public BusinessInstance $instance;
    public User $adminUser;

    /**
     * Create a new message instance.
     */
    public function __construct(BusinessInstance $instance, User $adminUser)
    {
        $this->instance = $instance;
        $this->adminUser = $adminUser;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva instancia registrada — ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $this->instance->loadMissing(['businessType', 'plan']);

        return new Content(
            view: 'emails.nueva-instancia-registrada',
            with: [
                'instance' => $this->instance,
                'adminName' => $this->adminUser->name,
                'adminEmail' => $this->adminUser->email,
                'instanceUrl' => route('instances.show', $this->instance),
                'loginUrl' => route('login'),
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
