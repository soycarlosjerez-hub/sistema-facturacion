<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Bypass for elevated roles (Spatie roles)
        if ($user->hasRole('admin') || $user->hasRole('owner') || $user->hasRole('admin-business')) {
            return $next($request);
        }

        // Bypass using User 'role' attribute (native field on users table)
        $nativeRole = $user->role ?? '';
        if (in_array($nativeRole, ['admin', 'owner', 'admin-business', 'root'])) {
            return $next($request);
        }

        // Check Spatie permissions
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return $next($request);
            }
        }

        // Fallback: check if user's InstanceRole grants visibility to the module
        // associated with any of the required permissions (e.g. 'productos.view' → 'inventario')
        $instanceRole = $user->instanceRole;
        if ($instanceRole) {
            $permToModule = [
                'dashboard.view'            => 'dashboard',
                'productos.view'            => 'inventario',
                'productos.create'          => 'inventario',
                'productos.edit'            => 'inventario',
                'productos.delete'          => 'inventario',
                'compras.view'              => 'compras',
                'compras.create'            => 'compras',
                'compras.edit'              => 'compras',
                'compras.delete'            => 'compras',
                'proveedores.view'          => 'proveedores',
                'proveedores.create'        => 'proveedores',
                'proveedores.edit'          => 'proveedores',
                'proveedores.delete'        => 'proveedores',
                'kardex.view'               => 'kardex',
                'ventas.create'             => 'ventas',
                'ventas.view'               => 'ventas',
                'clientes.view'             => 'clientes',
                'clientes.create'           => 'clientes',
                'clientes.edit'             => 'clientes',
                'clientes.delete'           => 'clientes',
                'cobros.view'               => 'cobros',
                'cajas.view'                => 'cajas',
                'cajas.create'              => 'cajas',
                'cajas.edit'                => 'cajas',
                'sucursales.view'           => 'sucursales',
                'almacenes.view'            => 'almacenes',
                'ncf.view'                  => 'ncf',
                'ncf.manage'                => 'ncf',
                'ecf.view'                  => 'ecf',
                'ecf.manage'                => 'ecf',
                'ecf.certificados'          => 'certificados-digitales',
                'reportes.view'             => 'reportes-ventas',
                'cotizaciones.view'         => 'cotizaciones',
                'conduces.view'             => 'conduces',
                'devoluciones.view'         => 'devoluciones',
                'gastos.view'               => 'gastos',
                'impresoras.view'           => 'impresoras',
                'listas-precio.view'        => 'listas-precio',
                'restaurante.view'          => 'restaurante',
                'restaurante.categorias'    => 'restaurante-categorias',
                'restaurante.reservaciones' => 'restaurante-reservaciones',
                'lavadero.view'             => 'lavadero',
                'lavadero.servicios'        => 'lavadero-servicios',
                'lavadero.vehiculos'        => 'lavadero-vehiculos',
                'lavadero.citas'            => 'lavadero-citas',
                'lavadero.lavadores'        => 'lavadero-lavadores',
                'auditoria.view'            => 'auditoria',
                'backups.view'              => 'backups',
                'configuracion.view'        => 'configuracion-general',
                'payment-processors.view'   => 'payment-processors',
                'delivery-companies.view'   => 'delivery-companies',
            ];

            foreach ($permissions as $permission) {
                $moduloKey = $permToModule[$permission] ?? null;
                if ($moduloKey && $instanceRole->isModuloVisible($moduloKey)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
