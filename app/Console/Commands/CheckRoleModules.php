<?php

namespace App\Console\Commands;

use App\Models\InstanceRole;
use App\Models\InstanceRoleModule;
use App\Models\Modulo;
use Illuminate\Console\Command;

class CheckRoleModules extends Command
{
    protected $signature = 'role:check {role_id?}';
    protected $description = 'Verifica los módulos de un rol de instancia';

    public function handle(): int
    {
        $roleId = $this->argument('role_id');

        if (!$roleId) {
            $this->info('=== Todos los InstanceRoles ===');
            $roles = InstanceRole::with('businessInstance')->get();
            if ($roles->isEmpty()) {
                $this->warn('NO hay InstanceRoles en la base de datos');
                return 0;
            }
            foreach ($roles as $r) {
                $modCount = $r->modules->count();
                $visCount = $r->visibleModules->count();
                $this->line("  ID {$r->id}: {$r->name} | Instancia: {$r->businessInstance->nombre} (ID:{$r->business_instance_id}) | Mods: {$modCount} ({$visCount} visibles)");
            }
            return 0;
        }

        $role = InstanceRole::with('modules', 'businessInstance')->find($roleId);
        if (!$role) {
            $this->error("❌ InstanceRole ID {$roleId} NO ENCONTRADO");
            $this->newLine();
            $this->warn('Buscando si existe con otro business_instance_id...');
            $all = InstanceRoleModule::where('instance_role_id', $roleId)->get();
            if ($all->isEmpty()) {
                $this->warn('No hay InstanceRoleModule con ese role_id tampoco.');
            } else {
                foreach ($all as $am) {
                    $this->line("  Module: {$am->modulo_key} (visible: " . ($am->is_visible ? 'SI' : 'NO') . ')');
                }
            }
            return 1;
        }

        $this->info("✅ Rol encontrado: {$role->name}");
        $this->info("   Instancia: {$role->businessInstance->nombre} (ID: {$role->business_instance_id})");
        $this->newLine();

        $this->warn('--- Módulos asignados ---');
        if ($role->modules->isEmpty()) {
            $this->error('⚠️ ESTE ROL NO TIENE NINGÚN MÓDULO ASIGNADO');
        } else {
            foreach ($role->modules as $m) {
                $icon = $m->is_visible ? '✓' : '~';
                $this->line("  {$icon} {$m->modulo_key} (visible: " . ($m->is_visible ? 'SI' : 'NO') . ')');
            }
        }

        $this->newLine();
        $this->warn('--- Módulo CONTABILIDAD (ncf, ecf, etc.) ---');
        $contabilidadMods = ['ncf','ecf','secuencias-ecf','certificados-digitales','libros-ventas','libros-compras','reportes-retenciones','reportes-fiscales','reportes-resumen','formulario-14-14'];
        $tieneContabilidad = false;
        foreach ($contabilidadMods as $key) {
            $found = $role->modules->first(fn($m) => $m->modulo_key === $key);
            if ($found) {
                $this->info("  ✓ {$key} (" . ($found->is_visible ? 'VISIBLE' : 'OCULTO') . ')');
                $tieneContabilidad = true;
            } else {
                $this->error("  ✗ {$key} - NO ASIGNADO");
            }
        }

        if (!$tieneContabilidad) {
            $this->newLine();
            $this->error('⛔ ESTE ROL NO TIENE NINGÚN MÓDULO DE CONTABILIDAD!');
            $this->newLine();
            $this->warn('¿Desea agregar los módulos de contabilidad a este rol? (s/n)');
            if ($this->confirm('Agregar módulos de contabilidad', true)) {
                $keys = ['ncf','ecf','secuencias-ecf','certificados-digitales','libros-ventas','libros-compras','reportes-retenciones','reportes-fiscales','reportes-resumen','formulario-14-14'];
                foreach ($keys as $key) {
                    $existing = $role->modules()->where('modulo_key', $key)->first();
                    if ($existing) {
                        $existing->update(['is_visible' => true]);
                        $this->info("  ✓ Actualizado: {$key} a VISIBLE");
                    } else {
                        $role->modules()->create(['modulo_key' => $key, 'is_visible' => true]);
                        $this->info("  ✓ Creado: {$key} VISIBLE");
                    }
                }
                $this->newLine();
                $this->info('✅ Módulos de contabilidad agregados exitosamente!');
            }
            return 1;
        }

        $this->newLine();
        $this->info('✅ Este rol SÍ tiene módulos de contabilidad asignados.');
        return 0;
    }
}
