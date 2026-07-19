<?php

namespace App\Notifications;

use App\Models\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClienteResetPassword extends Notification
{
    use Queueable;

    public function __construct(
        protected string $token,
        protected string $email
    ) {}

    public function via(Cliente $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(Cliente $notifiable): MailMessage
    {
        $url = url('/api/auth/cliente/reset-password?token=' . $this->token . '&email=' . urlencode($this->email));

        return (new MailMessage)
            ->subject('Restablecer tu contraseña')
            ->greeting('¡Hola ' . $notifiable->nombre . '!')
            ->line('Recibimos una solicitud para restablecer tu contraseña.')
            ->action('Restablecer Contraseña', $url)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este mensaje.')
            ->salutation('Saludos, el equipo de soporte.');
    }
}
