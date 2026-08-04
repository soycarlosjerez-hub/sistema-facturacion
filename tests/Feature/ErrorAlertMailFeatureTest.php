<?php

namespace Tests\Feature;

use App\Mail\ErrorAlertMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ErrorAlertMailFeatureTest extends TestCase
{
    public function test_error_alert_mail_sends_to_configured_email()
    {
        Mail::fake();

        $mail = new ErrorAlertMail(
            level: 'critical',
            title: 'Feature Test Error',
            errorMessage: 'Testing error alert via feature test',
            exceptionClass: 'Tests\Feature\ErrorAlertMailFeatureTest',
            file: 'tests/Feature/ErrorAlertMailFeatureTest.php',
            line: 15,
            ipAddress: '127.0.0.1',
            userAgent: 'PHPUnit/10.0',
            context: ['feature_test' => true],
            source: 'test',
            createdAt: now()->format('Y-m-d H:i:s'),
            tenantName: 'Test Instance'
        );

        Mail::to('jcjerez@gmail.com')->send($mail);

        Mail::assertSent(ErrorAlertMail::class, function ($mailable) {
            $envelope = $mailable->envelope();
            return $mailable->hasTo('jcjerez@gmail.com')
                && str_contains($envelope->subject, '[CRÍTICO]');
        });
    }

    public function test_error_alert_is_sent_synchronously()
    {
        Mail::fake();

        $mail = new ErrorAlertMail(
            level: 'error',
            title: 'Queued Error Test',
            errorMessage: 'Testing queued error alert',
        );

        Mail::to('jcjerez@gmail.com')->send($mail);

        Mail::assertSent(ErrorAlertMail::class);
        Mail::assertNothingQueued();
    }
}
