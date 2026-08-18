<?php

namespace App\Listeners;

use App\Events\NcfExpiring;
use App\Services\NotificationService;

class NotifyNcfExpiring
{
    public function __construct(private NotificationService $notifications) {}

    public function handle(NcfExpiring $event): void
    {
        $this->notifications->notifyInstance(
            type: 'ncf_expiring',
            category: 'fiscal',
            title: 'NCF próximo a vencer: ' . $event->ncfNumber,
            body: sprintf('%s (%s) vence en %d día(s)', $event->ncfNumber, $event->type, $event->daysRemaining),
            extra: [
                'icon' => 'bi-calendar-x',
                'color' => $event->daysRemaining <= 7 ? '#ef4444' : '#f59e0b',
                'action_url' => route('ncf.index'),
                'category_icon' => 'bi-file-earmark-text',
                'category_label' => 'Fiscal',
                'verb' => 'detectó NCF por vencer',
            ],
            tenantId: $event->businessInstanceId,
            actor: null,
        );
    }
}