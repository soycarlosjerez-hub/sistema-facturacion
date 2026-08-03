<?php

namespace App\Listeners;

use App\Events\NcfExpiring;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyNcfExpiring implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(NcfExpiring $event): void
    {
        $userIds = \App\Models\User::whereIn('role', ['admin', 'admin-business', 'root', 'gerente'])
            ->pluck('id')
            ->toArray();

        foreach ($userIds as $userId) {
            UserNotification::createNotification(
                $userId,
                'ncf_expiring',
                'NCF próximo a vencer: ' . $event->ncfNumber,
                sprintf('%s (%s) vence en %d día(s)', $event->ncfNumber, $event->type, $event->daysRemaining),
                'fiscal',
                [
                    'icon' => 'bi-calendar-x',
                    'color' => $event->daysRemaining <= 7 ? '#ef4444' : '#f59e0b',
                    'action_url' => route('ncf.index'),
                    'category_icon' => 'bi-file-earmark-text',
                    'category_label' => 'Fiscal',
                ]
            );
        }
    }
}
