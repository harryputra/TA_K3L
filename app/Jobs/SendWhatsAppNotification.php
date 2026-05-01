<?php

namespace App\Jobs;

use App\Services\FonnteWhatsApp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public string $target,
        public string $message,
    ) {
    }

    public function handle(FonnteWhatsApp $whatsApp): void
    {
        $whatsApp->send($this->target, $this->message);
    }
}
