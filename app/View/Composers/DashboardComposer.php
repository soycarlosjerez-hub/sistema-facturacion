<?php

namespace App\View\Composers;

use App\Models\SystemSetting;
use Illuminate\View\View;

class DashboardComposer
{
    public function compose(View $view): void
    {
        $view->with('pctCambio', function ($actual, $anterior) {
            if ($anterior == 0) {
                return $actual > 0 ? 100 : 0;
            }
            return round((($actual - $anterior) / $anterior) * 100, 1);
        });

        $view->with('esPositivo', fn($valor) => $valor >= 0);

        $view->with('moneda', SystemSetting::monedaSimbolo() ?? 'RD$');
    }
}
