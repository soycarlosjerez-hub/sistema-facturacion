<?php

namespace App\Console\Commands;

use App\Events\NcfExpiring;
use App\Models\NcfSequence;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;

class CheckNcfExpiring extends Command
{
    protected $signature = 'ncf:check-expiring';
    protected $description = 'Check for expiring NCF sequences and trigger notifications';

    public function handle(): int
    {
        $expiring = NcfSequence::where('activo', true)
            ->where('fecha_vencimiento', '<=', now()->addDays(30))
            ->where('fecha_vencimiento', '>', now())
            ->get();

        foreach ($expiring as $ncf) {
            $diasRestantes = now()->diffInDays($ncf->fecha_vencimiento, false);
            Event::dispatch(new NcfExpiring(
                ncfNumber: $ncf->prefijo . '-' . str_pad($ncf->actual, 9, '0', STR_PAD_LEFT),
                type: $ncf->nombre,
                daysRemaining: max(0, $diasRestantes),
                expiryDate: $ncf->fecha_vencimiento->format('Y-m-d'),
                businessInstanceId: $ncf->tenant_id
            ));
        }

        $expired = NcfSequence::where('activo', true)
            ->where('fecha_vencimiento', '<=', now())
            ->get();

        foreach ($expired as $ncf) {
            Event::dispatch(new NcfExpiring(
                ncfNumber: $ncf->prefijo . '-' . str_pad($ncf->actual, 9, '0', STR_PAD_LEFT),
                type: $ncf->nombre,
                daysRemaining: 0,
                expiryDate: $ncf->fecha_vencimiento->format('Y-m-d'),
                businessInstanceId: $ncf->tenant_id
            ));
        }

        $this->info(sprintf(
            'Checked %d expiring + %d expired NCF sequences',
            $expiring->count(),
            $expired->count()
        ));

        return Command::SUCCESS;
    }
}
