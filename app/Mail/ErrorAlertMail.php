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
    public $errorMessage;
    public $exceptionClass;
    public $file;
    public $line;
    public $ipAddress;
    public $userAgent;
    public $context;
    public $source;
    public $createdAt;
    public $tenantName;
    public $tenantId;
    public $userId;
    public $userName;
    public $userEmail;
    public $userRole;
    public $httpMethod;
    public $url;
    public $referer;
    public $sessionId;
    public $inputs;

    public function __construct(
        string $level,
        string $title,
        string $errorMessage,
        ?string $exceptionClass = null,
        ?string $file = null,
        ?int $line = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        array $context = [],
        ?string $source = null,
        ?string $createdAt = null,
        ?string $tenantName = null,
        ?int $tenantId = null,
        ?int $userId = null,
        ?string $userName = null,
        ?string $userEmail = null,
        ?string $userRole = null,
        ?string $httpMethod = null,
        ?string $url = null,
        ?string $referer = null,
        ?string $sessionId = null,
        ?array $inputs = null
    ) {
        $this->level = $level;
        $this->title = $title;
        $this->errorMessage = $errorMessage;
        $this->exceptionClass = $exceptionClass;
        $this->file = $file;
        $this->line = $line;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->context = $context;
        $this->source = $source;
        $this->createdAt = $createdAt;
        $this->tenantName = $tenantName;
        $this->tenantId = $tenantId;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userRole = $userRole;
        $this->httpMethod = $httpMethod;
        $this->url = $url;
        $this->referer = $referer;
        $this->sessionId = $sessionId;
        $this->inputs = $inputs;
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
            with: [
                'level' => $this->level,
                'title' => $this->title,
                'errorMessage' => $this->errorMessage,
                'exceptionClass' => $this->exceptionClass,
                'file' => $this->file,
                'line' => $this->line,
                'ipAddress' => $this->ipAddress,
                'userAgent' => $this->userAgent,
                'context' => $this->context,
                'source' => $this->source,
                'createdAt' => $this->createdAt,
                'tenantName' => $this->tenantName,
                'tenantId' => $this->tenantId,
                'userId' => $this->userId,
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'userRole' => $this->userRole,
                'httpMethod' => $this->httpMethod,
                'url' => $this->url,
                'referer' => $this->referer,
                'sessionId' => $this->sessionId,
                'inputs' => $this->inputs,
            ],
        );
    }

    public function middleware(): array
    {
        return [new \App\Mail\Middleware\ApplyGlobalSmtpConfig()];
    }
}
