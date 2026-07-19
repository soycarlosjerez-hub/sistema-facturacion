<?php

namespace App\Notifications;

use App\Models\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClienteVerifyEmail extends Notification
{
    use Queueable;

    public function __construct(
        protected string $verificationUrl
    ) {}

    public function via(Cliente $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(Cliente $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifica tu correo electrónico')
            ->greeting('¡Hola ' . $notifiable->nombre . '!')
            ->line('Gracias por registrarte. Por favor verifica tu correo electrónico haciendo clic en el botón de abajo.')
            ->action('Verificar Email', $this->verificationUrl)
            ->line('Si no creaste esta cuenta, puedes ignorar este mensaje.')
            ->salutation('Saludos, el equipo de soporte.');
    }
}
