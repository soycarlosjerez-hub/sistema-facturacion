<?php

namespace Tests\Unit;

use App\Mail\ErrorAlertMail;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\TestCase;

class ErrorAlertMailTest extends TestCase
{
    public function test_mailable_has_correct_subject_for_critical_level()
    {
        $mail = new ErrorAlertMail(
            level: 'critical',
            title: 'Test Critical Error',
            message: 'This is a test critical error message',
            exceptionClass: 'App\Exceptions\TestException',
            file: 'tests/TestException.php',
            line: 42,
            ipAddress: '127.0.0.1',
            userAgent: 'Test Browser',
            context: ['test' => 'true'],
            source: 'test',
            createdAt: now()->format('Y-m-d H:i:s'),
            tenantName: 'Test Tenant'
        );

        $envelope = $mail->envelope();
        $this->assertEquals('[CRÍTICO] Test Critical Error', $envelope->subject);
    }

    public function test_mailable_has_correct_subject_for_error_level()
    {
        $mail = new ErrorAlertMail(
            level: 'error',
            title: 'Test Error',
            message: 'This is a test error message',
        );

        $envelope = $mail->envelope();
        $this->assertEquals('[ERROR] Test Error', $envelope->subject);
    }

    public function test_mailable_has_correct_subject_for_warning_level()
    {
        $mail = new ErrorAlertMail(
            level: 'warning',
            title: 'Test Warning',
            message: 'This is a test warning message',
        );

        $envelope = $mail->envelope();
        $this->assertEquals('[ADVERTENCIA] Test Warning', $envelope->subject);
    }

    public function test_mailable_content_view()
    {
        $mail = new ErrorAlertMail(
            level: 'error',
            title: 'Test Error',
            message: 'Test message',
        );

        $this->assertEquals('emails.error-alert', $mail->content()->view);
    }

    public function test_mailable_stores_all_properties()
    {
        $now = now()->format('Y-m-d H:i:s');
        $mail = new ErrorAlertMail(
            level: 'critical',
            title: 'Critical Title',
            message: 'Critical Message',
            exceptionClass: 'App\Exceptions\TestException',
            file: 'tests/File.php',
            line: 100,
            ipAddress: '192.168.1.1',
            userAgent: 'Chrome/120',
            context: ['key' => 'value'],
            source: 'test',
            createdAt: $now,
            tenantName: 'Test Company'
        );

        $this->assertEquals('critical', $mail->level);
        $this->assertEquals('Critical Title', $mail->title);
        $this->assertEquals('Critical Message', $mail->message);
        $this->assertEquals('App\Exceptions\TestException', $mail->exceptionClass);
        $this->assertEquals('tests/File.php', $mail->file);
        $this->assertEquals(100, $mail->line);
        $this->assertEquals('192.168.1.1', $mail->ipAddress);
        $this->assertEquals('Chrome/120', $mail->userAgent);
        $this->assertEquals(['key' => 'value'], $mail->context);
        $this->assertEquals('test', $mail->source);
        $this->assertEquals($now, $mail->createdAt);
        $this->assertEquals('Test Company', $mail->tenantName);
    }
}
