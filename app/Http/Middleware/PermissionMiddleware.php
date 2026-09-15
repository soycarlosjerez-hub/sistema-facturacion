<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Bypass solo para super-admin global (owner/root). Los roles de tenant
        // (admin, admin-business, vendedor) deben pasar por permisos Spatie
        // o por visibilidad de módulo del InstanceRole.
        if (! $user->hasRole('owner') && ! $user->hasRole('root')) {
            // Check Spatie permissions
            foreach ($permissions as $permission) {
                if ($user->can($permission)) {
                    return $next($request);
                }
            }
        } else {
            return $next($request);
        }

        // Fallback: check if user's InstanceRole grants visibility to the module
        // associated with any of the required permissions (e.g. 'productos.view' → 'inventario')
        $instanceRole = $user->instanceRole;
        if ($instanceRole) {
            $permToModule = [
                'dashboard.view' => 'dashboard',
                'productos.view' => 'inventario',
                'productos.create' => 'inventario',
                'productos.edit' => 'inventario',
                'productos.delete' => 'inventario',
                'compras.view' => 'compras',
                'compras.create' => 'compras',
                'compras.edit' => 'compras',
                'compras.delete' => 'compras',
                'proveedores.view' => 'proveedores',
                'proveedores.create' => 'proveedores',
                'proveedores.edit' => 'proveedores',
                'proveedores.delete' => 'proveedores',
                'kardex.view' => 'kardex',
                'ventas.create' => 'ventas',
                'ventas.view' => 'ventas',
                'clientes.view' => 'clientes',
                'clientes.create' => 'clientes',
                'clientes.edit' => 'clientes',
                'clientes.delete' => 'clientes',
                'cobros.view' => 'cobros',
                'cajas.view' => 'cajas',
                'cajas.create' => 'cajas',
                'cajas.edit' => 'cajas',
                'sucursales.view' => 'sucursales',
                'almacenes.view' => 'almacenes',
                'ncf.view' => 'ncf',
                'ncf.manage' => 'ncf',
                'ecf.view' => 'ecf',
                'ecf.manage' => 'ecf',
                'ecf.certificados' => 'certificados-digitales',
                'reportes.view' => 'reportes-ventas',
                'cotizaciones.view' => 'cotizaciones',
                'conduces.view' => 'conduces',
                'devoluciones.view' => 'devoluciones',
                'gastos.view' => 'gastos',
                'impresoras.view' => 'impresoras',
                'listas-precio.view' => 'listas-precio',
                'restaurante.view' => 'restaurante',
                'restaurante.categorias' => 'restaurante-categorias',
                'restaurante.reservaciones' => 'restaurante-reservaciones',
                'lavadero.view' => 'lavadero',
                'lavadero.servicios' => 'lavadero-servicios',
                'lavadero.vehiculos' => 'lavadero-vehiculos',
                'lavadero.citas' => 'lavadero-citas',
                'lavadero.lavadores' => 'lavadero-lavadores',
                'auditoria.view' => 'auditoria',
                'backups.view' => 'backups',
                'configuracion.view' => 'configuracion-general',
                'payment-processors.view' => 'payment-processors',
                'delivery-companies.view' => 'delivery-companies',
                'cuentas-bancarias.view' => 'cuentas-bancarias',
                'cuentas-bancarias.create' => 'cuentas-bancarias',
                'cuentas-bancarias.edit' => 'cuentas-bancarias',
                'cuentas-bancarias.delete' => 'cuentas-bancarias',
            ];

            foreach ($permissions as $permission) {
                $moduloKey = array_key_exists($permission, $permToModule) ? $permToModule[$permission] : null;
                if ($moduloKey) {
                    try {
                        if ($instanceRole->isModuloVisible($moduloKey)) {
                            return $next($request);
                        }
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
