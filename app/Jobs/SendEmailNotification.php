<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;
    public $backoff = [30, 60, 120];

    protected string $view;
    protected array $data;
    protected string $to;
    protected string $subject;

    public function __construct(string $view, array $data, string $to, string $subject)
    {
        $this->view = $view;
        $this->data = $data;
        $this->to = $to;
        $this->subject = $subject;
    }

    public function handle(): void
    {
        $view = $this->view;
        $data = $this->data;

        Mail::send([], [], function ($message) use ($view, $data) {
            $message->to($this->to)
                ->subject($this->subject)
                ->html(view($view, $data)->render());
        });
    }
}
