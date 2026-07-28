<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $level;
    public $title;
    public $message;
    public $exceptionClass;
    public $file;
    public $line;
    public $ipAddress;
    public $userAgent;
    public $context;
    public $source;
    public $createdAt;
    public $tenantName;

    public function __construct(
        string $level,
        string $title,
        string $message,
        ?string $exceptionClass = null,
        ?string $file = null,
        ?int $line = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        array $context = [],
        ?string $source = null,
        ?string $createdAt = null,
        ?string $tenantName = null
    ) {
        $this->level = $level;
        $this->title = $title;
        $this->message = $message;
        $this->exceptionClass = $exceptionClass;
        $this->file = $file;
        $this->line = $line;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->context = $context;
        $this->source = $source;
        $this->createdAt = $createdAt;
        $this->tenantName = $tenantName;
    }

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        $prefix = match ($this->level) {
            'critical' => '[CRÍTICO]',
            'error' => '[ERROR]',
            'warning' => '[ADVERTENCIA]',
            default => '[ALERTA]',
        };

        return new \Illuminate\Mail\Mailables\Envelope(
            subject: "{$prefix} {$this->title}",
        );
    }

    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.error-alert',
        );
    }
}
