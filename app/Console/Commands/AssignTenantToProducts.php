<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignTenantToProducts extends Command
{
    protected $signature = 'products:assign-tenant {--instance= : ID de la instancia de negocio (default: usuario autenticado)}';
    protected $description = 'Asignar tenant_id a los productos sin instancia para el usuario/estado actual';

    public function handle()
    {
        $instanceId = $this->option('instance');
        
        if (!$instanceId && Auth::check()) {
            $instanceId = Auth::user()->business_instance_id;
        }
        
        if (!$instanceId) {
            $this->error('No se pudo determinar la instancia de negocio.');
            $this->comment('Use --instance=<id> para especificar la instancia.');
            return 1;
        }
        
        $count = DB::table('productos')
            ->whereNull('tenant_id')
            ->update(['tenant_id' => $instanceId]);
        
        $this->info("Asignados {$count} productos a la instancia {$instanceId}.");
    }
}
