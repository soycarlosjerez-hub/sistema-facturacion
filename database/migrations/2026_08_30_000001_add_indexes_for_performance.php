<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega índices de rendimiento para optimizar queries frecuentemente usadas
     * en las tablas principales del ERP. Mejora el rendimiento de filtros,
     * joins y ordenamientos en tablas de ventas, compras, clientes, productos,
     * inventario y autenticación.
     */
    public function up(): void
    {
        // ==========================================
        // VENTAS - Ventas principales
        // ==========================================
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'tenant_id') && !Schema::hasIndex('ventas', 'ventas_tenant_id_idx')) {
                $table->index('tenant_id', 'ventas_tenant_id_idx');
            } // Filtro principal en queries multi-tenant
            if (Schema::hasColumn('ventas', 'user_id') && !Schema::hasIndex('ventas', 'ventas_user_id_idx')) {
                $table->index('user_id', 'ventas_user_id_idx');
            } // Filtro por vendedor en reportes
            if (Schema::hasColumn('ventas', 'cliente_id') && !Schema::hasIndex('ventas', 'ventas_cliente_id_idx')) {
                $table->index('cliente_id', 'ventas_cliente_id_idx');
            } // Búsqueda de ventas por cliente
            if (Schema::hasColumn('ventas', 'estado') && !Schema::hasIndex('ventas', 'ventas_estado_idx')) {
                $table->index('estado', 'ventas_estado_idx');
            } // Filtro de estado en lista de ventas
            if (Schema::hasColumn('ventas', 'tipo_comprobante') && !Schema::hasIndex('ventas', 'ventas_tipo_comprobante_idx')) {
                $table->index('tipo_comprobante', 'ventas_tipo_comprobante_idx');
            } // Filtrado por tipo de comprobante fiscal
            if (Schema::hasColumn('ventas', 'ncf') && !Schema::hasIndex('ventas', 'ventas_ncf_idx')) {
                $table->index('ncf', 'ventas_ncf_idx');
            } // Búsqueda rápida por NCF
            if (Schema::hasColumn('ventas', 'fecha') && Schema::hasColumn('ventas', 'tenant_id') && !Schema::hasIndex('ventas', 'ventas_fecha_tenant_idx')) {
                $table->index(['fecha', 'tenant_id'], 'ventas_fecha_tenant_idx');
            } // Rango de fechas filtrando por tenant
            if (Schema::hasColumn('ventas', 'created_at') && !Schema::hasIndex('ventas', 'ventas_created_at_idx')) {
                $table->index('created_at', 'ventas_created_at_idx');
            } // Ordenamiento por fecha de creacion, filtros de fecha reciente
        });

        // ==========================================
        // VENTA_DETALLES - Detalles de ventas
        // ==========================================
        Schema::table('venta_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('venta_detalles', 'venta_id') && !Schema::hasIndex('venta_detalles', 'venta_detalles_venta_id_idx')) {
                $table->index('venta_id', 'venta_detalles_venta_id_idx');
            } // Join con tabla de ventas principal
            if (Schema::hasColumn('venta_detalles', 'producto_id') && !Schema::hasIndex('venta_detalles', 'venta_detalles_producto_id_idx')) {
                $table->index('producto_id', 'venta_detalles_producto_id_idx');
            } // Consulta de historial de ventas por producto
            if (Schema::hasColumn('venta_detalles', 'tenant_id') && !Schema::hasIndex('venta_detalles', 'venta_detalles_tenant_id_idx')) {
                $table->index('tenant_id', 'venta_detalles_tenant_id_idx');
            } // Filtro multi-tenant en detalles
        });

        // ==========================================
        // PAGOS - Pagos de ventas
        // ==========================================
        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasColumn('pagos', 'venta_id') && !Schema::hasIndex('pagos', 'pagos_venta_id_idx')) {
                $table->index('venta_id', 'pagos_venta_id_idx');
            } // Join con ventas para ver historial de pagos
            if (Schema::hasColumn('pagos', 'metodo_pago') && !Schema::hasIndex('pagos', 'pagos_metodo_pago_idx')) {
                $table->index('metodo_pago', 'pagos_metodo_pago_idx');
            } // Resumen de cobros por metodo de pago
            if (Schema::hasColumn('pagos', 'user_id') && !Schema::hasIndex('pagos', 'pagos_user_id_idx')) {
                $table->index('user_id', 'pagos_user_id_idx');
            } // Reportes de pagos por cajero/vendedor
            if (Schema::hasColumn('pagos', 'fecha_pago') && !Schema::hasIndex('pagos', 'pagos_fecha_pago_idx')) {
                $table->index('fecha_pago', 'pagos_fecha_pago_idx');
            } // Rango de fechas para conciliacion
            if (Schema::hasColumn('pagos', 'tenant_id') && !Schema::hasIndex('pagos', 'pagos_tenant_id_idx')) {
                $table->index('tenant_id', 'pagos_tenant_id_idx');
            } // Filtro multi-tenant
        });

        // ==========================================
        // CLIENTES - Clientes del sistema
        // ==========================================
        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'tenant_id') && !Schema::hasIndex('clientes', 'clientes_tenant_id_idx')) {
                $table->index('tenant_id', 'clientes_tenant_id_idx');
            } // Filtro multi-tenant principal
            if (Schema::hasColumn('clientes', 'cedula_rnc') && !Schema::hasIndex('clientes', 'clientes_cedula_rnc_idx')) {
                $table->index('cedula_rnc', 'clientes_cedula_rnc_idx');
            } // Busqueda rapida por cedula o RNC del cliente
            if (Schema::hasColumn('clientes', 'tipo') && !Schema::hasIndex('clientes', 'clientes_tipo_idx')) {
                $table->index('tipo', 'clientes_tipo_idx');
            } // Segmentacion por tipo de cliente (fiscal/consumo)
            if (Schema::hasColumn('clientes', 'nombre') && !Schema::hasIndex('clientes', 'clientes_nombre_idx')) {
                $table->index('nombre', 'clientes_nombre_idx');
            } // Busqueda por nombre del cliente
        });

        // ==========================================
        // PRODUCTOS - Productos del inventario
        // ==========================================
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'tenant_id') && !Schema::hasIndex('productos', 'productos_tenant_id_idx')) {
                $table->index('tenant_id', 'productos_tenant_id_idx');
            } // Filtro multi-tenant principal
            if (Schema::hasColumn('productos', 'sku') && !Schema::hasIndex('productos', 'productos_sku_idx')) {
                $table->index('sku', 'productos_sku_idx');
            } // Busqueda rapida por codigo de producto
            if (Schema::hasColumn('productos', 'categoria_id') && !Schema::hasIndex('productos', 'productos_categoria_id_idx')) {
                $table->index('categoria_id', 'productos_categoria_id_idx');
            } // Listado de productos por categoria
            if (Schema::hasColumn('productos', 'is_active') && !Schema::hasIndex('productos', 'productos_is_active_idx')) {
                $table->index('is_active', 'productos_is_active_idx');
            } // Filtrar productos activos/inactivos
        });

        // ==========================================
        // COMPRAS - Compras a proveedores
        // ==========================================
        Schema::table('compras', function (Blueprint $table) {
            if (Schema::hasColumn('compras', 'tenant_id') && !Schema::hasIndex('compras', 'compras_tenant_id_idx')) {
                $table->index('tenant_id', 'compras_tenant_id_idx');
            } // Filtro multi-tenant principal
            if (Schema::hasColumn('compras', 'proveedor_id') && !Schema::hasIndex('compras', 'compras_proveedor_id_idx')) {
                $table->index('proveedor_id', 'compras_proveedor_id_idx');
            } // Historial de compras por proveedor
            if (Schema::hasColumn('compras', 'estado') && !Schema::hasIndex('compras', 'compras_estado_idx')) {
                $table->index('estado', 'compras_estado_idx');
            } // Filtrado de estado en lista de compras
            if (Schema::hasColumn('compras', 'fecha') && !Schema::hasIndex('compras', 'compras_fecha_idx')) {
                $table->index('fecha', 'compras_fecha_idx');
            } // Rango de fechas para reportes de compras
        });

        // ==========================================
        // COMPRA_DETALLES - Detalles de compras
        // ==========================================
        Schema::table('compra_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('compra_detalles', 'compra_id') && !Schema::hasIndex('compra_detalles', 'compra_detalles_compra_id_idx')) {
                $table->index('compra_id', 'compra_detalles_compra_id_idx');
            } // Join con tabla de compras principal
            if (Schema::hasColumn('compra_detalles', 'producto_id') && !Schema::hasIndex('compra_detalles', 'compra_detalles_producto_id_idx')) {
                $table->index('producto_id', 'compra_detalles_producto_id_idx');
            } // Historial de compras por producto
            if (Schema::hasColumn('compra_detalles', 'tenant_id') && !Schema::hasIndex('compra_detalles', 'compra_detalles_tenant_id_idx')) {
                $table->index('tenant_id', 'compra_detalles_tenant_id_idx');
            } // Filtro multi-tenant
        });

        // ==========================================
        // CAJAS - Cajas del sistema
        // ==========================================
        Schema::table('cajas', function (Blueprint $table) {
            if (Schema::hasColumn('cajas', 'tenant_id') && !Schema::hasIndex('cajas', 'cajas_tenant_id_idx')) {
                $table->index('tenant_id', 'cajas_tenant_id_idx');
            } // Filtro multi-tenant principal
            if (Schema::hasColumn('cajas', 'sucursal_id') && !Schema::hasIndex('cajas', 'cajas_sucursal_id_idx')) {
                $table->index('sucursal_id', 'cajas_sucursal_id_idx');
            } // Listado de cajas por sucursal
            if (Schema::hasColumn('cajas', 'activo') && !Schema::hasIndex('cajas', 'cajas_activo_idx')) {
                $table->index('activo', 'cajas_activo_idx');
            } // Filtrar cajas activas para operaciones
            if (Schema::hasColumn('cajas', 'estado') && !Schema::hasIndex('cajas', 'cajas_estado_idx')) {
                $table->index('estado', 'cajas_estado_idx');
            } // Estado de la caja (abierta/cerrada)
        });

        // ==========================================
        // SESION_CAjas - Sesiones de caja
        // ==========================================
        Schema::table('sesion_cajas', function (Blueprint $table) {
            if (Schema::hasColumn('sesion_cajas', 'caja_id') && !Schema::hasIndex('sesion_cajas', 'sesion_cajas_caja_id_idx')) {
                $table->index('caja_id', 'sesion_cajas_caja_id_idx');
            } // Join con tabla de cajas
            if (Schema::hasColumn('sesion_cajas', 'user_id') && !Schema::hasIndex('sesion_cajas', 'sesion_cajas_user_id_idx')) {
                $table->index('user_id', 'sesion_cajas_user_id_idx');
            } // Reportes de sesiones por usuario
            if (Schema::hasColumn('sesion_cajas', 'estado') && !Schema::hasIndex('sesion_cajas', 'sesion_cajas_estado_idx')) {
                $table->index('estado', 'sesion_cajas_estado_idx');
            } // Filtrado de sesiones abiertas/cerradas
            if (Schema::hasColumn('sesion_cajas', 'tenant_id') && !Schema::hasIndex('sesion_cajas', 'sesion_cajas_tenant_id_idx')) {
                $table->index('tenant_id', 'sesion_cajas_tenant_id_idx');
            } // Filtro multi-tenant
            if (Schema::hasColumn('sesion_cajas', 'fecha_apertura') && !Schema::hasIndex('sesion_cajas', 'sesion_cajas_fecha_apertura_idx')) {
                $table->index('fecha_apertura', 'sesion_cajas_fecha_apertura_idx');
            } // Busqueda por rango de fechas de apertura
        });

        // ==========================================
        // SUCURSALES - Sucursales del negocio
        // ==========================================
        Schema::table('sucursales', function (Blueprint $table) {
            if (Schema::hasColumn('sucursales', 'tenant_id') && !Schema::hasIndex('sucursales', 'sucursales_tenant_id_idx')) {
                $table->index('tenant_id', 'sucursales_tenant_id_idx');
            } // Filtro multi-tenant principal
            if (Schema::hasColumn('sucursales', 'activo') && !Schema::hasIndex('sucursales', 'sucursales_activo_idx')) {
                $table->index('activo', 'sucursales_activo_idx');
            } // Filtrar sucursales activas
        });

        // ==========================================
        // ALMACENES - Almacenes del sistema
        // ==========================================
        Schema::table('almacenes', function (Blueprint $table) {
            if (Schema::hasColumn('almacenes', 'tenant_id') && !Schema::hasIndex('almacenes', 'almacenes_tenant_id_idx')) {
                $table->index('tenant_id', 'almacenes_tenant_id_idx');
            } // Filtro multi-tenant principal
            if (Schema::hasColumn('almacenes', 'sucursal_id') && !Schema::hasIndex('almacenes', 'almacenes_sucursal_id_idx')) {
                $table->index('sucursal_id', 'almacenes_sucursal_id_idx');
            } // Listado de almacenes por sucursal
        });

        // ==========================================
        // ALMACEN_MOVIMIENTOS - Movimientos de inventario
        // ==========================================
        Schema::table('almacen_movimientos', function (Blueprint $table) {
            if (Schema::hasColumn('almacen_movimientos', 'tenant_id') && !Schema::hasIndex('almacen_movimientos', 'almacen_movimientos_tenant_id_idx')) {
                $table->index('tenant_id', 'almacen_movimientos_tenant_id_idx');
            } // Filtro multi-tenant principal
            if (Schema::hasColumn('almacen_movimientos', 'producto_id') && !Schema::hasIndex('almacen_movimientos', 'almacen_movimientos_producto_id_idx')) {
                $table->index('producto_id', 'almacen_movimientos_producto_id_idx');
            } // Historial de movimientos por producto
            if (Schema::hasColumn('almacen_movimientos', 'almacen_id') && !Schema::hasIndex('almacen_movimientos', 'almacen_movimientos_almacen_id_idx')) {
                $table->index('almacen_id', 'almacen_movimientos_almacen_id_idx');
            } // Historial de movimientos por almacen
            if (Schema::hasColumn('almacen_movimientos', 'fecha') && !Schema::hasIndex('almacen_movimientos', 'almacen_movimientos_fecha_idx')) {
                $table->index('fecha', 'almacen_movimientos_fecha_idx');
            } // Rango de fechas para reportes de inventario
        });

        // ==========================================
        // NCF_SEQUENCES - Secuencias de NCF fiscales
        // ==========================================
        Schema::table('ncf_sequences', function (Blueprint $table) {
            if (Schema::hasColumn('ncf_sequences', 'tenant_id') && !Schema::hasIndex('ncf_sequences', 'ncf_sequences_tenant_id_idx')) {
                $table->index('tenant_id', 'ncf_sequences_tenant_id_idx');
            } // Filtro multi-tenant principal
            if (Schema::hasColumn('ncf_sequences', 'ncf_tipo') && !Schema::hasIndex('ncf_sequences', 'ncf_sequences_ncf_tipo_idx')) {
                $table->index('ncf_tipo', 'ncf_sequences_ncf_tipo_idx');
            } // Busqueda por tipo de secuencia NCF
            if (Schema::hasColumn('ncf_sequences', 'tipo_comprobante') && !Schema::hasIndex('ncf_sequences', 'ncf_sequences_tipo_comprobante_idx')) {
                $table->index('tipo_comprobante', 'ncf_sequences_tipo_comprobante_idx');
            } // NCF para tipo de comprobante especifico
        });

        // ==========================================
        // ROLES - Tabla de roles (Spatie)
        // ==========================================
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'guard_name') && !Schema::hasIndex('roles', 'roles_guard_name_idx')) {
                $table->index('guard_name', 'roles_guard_name_idx');
            } // Busqueda de roles por guard (web/api)
            if (Schema::hasColumn('roles', 'name') && !Schema::hasIndex('roles', 'roles_name_idx')) {
                $table->index('name', 'roles_name_idx');
            } // Busqueda rapida por nombre de rol
        });

        // ==========================================
        // PERMISSIONS - Tabla de permisos (Spatie)
        // ==========================================
        Schema::table('permissions', function (Blueprint $table) {
            if (Schema::hasColumn('permissions', 'guard_name') && !Schema::hasIndex('permissions', 'permissions_guard_name_idx')) {
                $table->index('guard_name', 'permissions_guard_name_idx');
            } // Busqueda de permisos por guard (web/api)
            if (Schema::hasColumn('permissions', 'name') && !Schema::hasIndex('permissions', 'permissions_name_idx')) {
                $table->index('name', 'permissions_name_idx');
            } // Busqueda rapida por nombre de permiso
        });

        // ==========================================
        // MODEL_HAS_ROLES - Pivot de roles (Spatie)
        // ==========================================
        Schema::table('model_has_roles', function (Blueprint $table) {
            if (Schema::hasColumn('model_has_roles', 'role_id') && !Schema::hasIndex('model_has_roles', 'model_has_roles_role_id_idx')) {
                $table->index('role_id', 'model_has_roles_role_id_idx');
            } // Busqueda de modelos asignados a un rol
            if (Schema::hasColumn('model_has_roles', 'model_type') && !Schema::hasIndex('model_has_roles', 'model_has_roles_model_type_idx')) {
                $table->index('model_type', 'model_has_roles_model_type_idx');
            } // Filtrado por tipo de modelo (User, etc.)
            if (Schema::hasColumn('model_has_roles', 'model_id') && !Schema::hasIndex('model_has_roles', 'model_has_roles_model_id_idx')) {
                $table->index('model_id', 'model_has_roles_model_id_idx');
            } // Busqueda directa por ID de modelo
        });

        // ==========================================
        // ROLE_HAS_PERMISSIONS - Pivot de permisos (Spatie)
        // ==========================================
        Schema::table('role_has_permissions', function (Blueprint $table) {
            if (Schema::hasColumn('role_has_permissions', 'role_id') && !Schema::hasIndex('role_has_permissions', 'role_has_permissions_role_id_idx')) {
                $table->index('role_id', 'role_has_permissions_role_id_idx');
            } // Permisos asignados a un rol
            if (Schema::hasColumn('role_has_permissions', 'permission_id') && !Schema::hasIndex('role_has_permissions', 'role_has_permissions_permission_id_idx')) {
                $table->index('permission_id', 'role_has_permissions_permission_id_idx');
            } // Roles que tienen un permiso especifico
        });

        // ==========================================
        // USERS - Tabla principal de usuarios
        // ==========================================
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email') && !Schema::hasIndex('users', 'users_email_idx')) {
                $table->index('email', 'users_email_idx');
            } // Autenticacion rapida por email (login)
            if (Schema::hasColumn('users', 'business_instance_id') && !Schema::hasIndex('users', 'users_business_instance_id_idx')) {
                $table->index('business_instance_id', 'users_business_instance_id_idx');
            } // Usuarios de una instancia de negocio especifica
            if (Schema::hasColumn('users', 'role') && !Schema::hasIndex('users', 'users_role_idx')) {
                $table->index('role', 'users_role_idx');
            } // Filtro de usuarios por rol para gestion administrativa
            // SKIPPED: two_factor_secret is TEXT column — cannot be indexed without prefix length
            // if (Schema::hasColumn('users', 'two_factor_secret')) {
            //     $table->index(['two_factor_secret'], 'users_two_factor_secret_idx', 50);
            // } // Busqueda rapida para verificar y gestionar 2FA
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasIndex('ventas', 'ventas_tenant_id_idx')) {
                $table->dropIndex('ventas_tenant_id_idx');
            }
            if (Schema::hasIndex('ventas', 'ventas_user_id_idx')) {
                $table->dropIndex('ventas_user_id_idx');
            }
            if (Schema::hasIndex('ventas', 'ventas_cliente_id_idx')) {
                $table->dropIndex('ventas_cliente_id_idx');
            }
            if (Schema::hasIndex('ventas', 'ventas_estado_idx')) {
                $table->dropIndex('ventas_estado_idx');
            }
            if (Schema::hasIndex('ventas', 'ventas_tipo_comprobante_idx')) {
                $table->dropIndex('ventas_tipo_comprobante_idx');
            }
            if (Schema::hasIndex('ventas', 'ventas_ncf_idx')) {
                $table->dropIndex('ventas_ncf_idx');
            }
            if (Schema::hasIndex('ventas', 'ventas_fecha_tenant_idx')) {
                $table->dropIndex('ventas_fecha_tenant_idx');
            }
            if (Schema::hasIndex('ventas', 'ventas_created_at_idx')) {
                $table->dropIndex('ventas_created_at_idx');
            }
        });

        Schema::table('venta_detalles', function (Blueprint $table) {
            if (Schema::hasIndex('venta_detalles', 'venta_detalles_venta_id_idx')) {
                $table->dropIndex('venta_detalles_venta_id_idx');
            }
            if (Schema::hasIndex('venta_detalles', 'venta_detalles_producto_id_idx')) {
                $table->dropIndex('venta_detalles_producto_id_idx');
            }
            if (Schema::hasIndex('venta_detalles', 'venta_detalles_tenant_id_idx')) {
                $table->dropIndex('venta_detalles_tenant_id_idx');
            }
        });

        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasIndex('pagos', 'pagos_venta_id_idx')) {
                $table->dropIndex('pagos_venta_id_idx');
            }
            if (Schema::hasIndex('pagos', 'pagos_metodo_pago_idx')) {
                $table->dropIndex('pagos_metodo_pago_idx');
            }
            if (Schema::hasIndex('pagos', 'pagos_user_id_idx')) {
                $table->dropIndex('pagos_user_id_idx');
            }
            if (Schema::hasIndex('pagos', 'pagos_fecha_pago_idx')) {
                $table->dropIndex('pagos_fecha_pago_idx');
            }
            if (Schema::hasIndex('pagos', 'pagos_tenant_id_idx')) {
                $table->dropIndex('pagos_tenant_id_idx');
            }
        });

        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasIndex('clientes', 'clientes_tenant_id_idx')) {
                $table->dropIndex('clientes_tenant_id_idx');
            }
            if (Schema::hasIndex('clientes', 'clientes_cedula_rnc_idx')) {
                $table->dropIndex('clientes_cedula_rnc_idx');
            }
            if (Schema::hasIndex('clientes', 'clientes_tipo_idx')) {
                $table->dropIndex('clientes_tipo_idx');
            }
            if (Schema::hasIndex('clientes', 'clientes_nombre_idx')) {
                $table->dropIndex('clientes_nombre_idx');
            }
        });

        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasIndex('productos', 'productos_tenant_id_idx')) {
                $table->dropIndex('productos_tenant_id_idx');
            }
            if (Schema::hasIndex('productos', 'productos_sku_idx')) {
                $table->dropIndex('productos_sku_idx');
            }
            if (Schema::hasIndex('productos', 'productos_categoria_id_idx')) {
                $table->dropIndex('productos_categoria_id_idx');
            }
            if (Schema::hasIndex('productos', 'productos_is_active_idx')) {
                $table->dropIndex('productos_is_active_idx');
            }
        });

        Schema::table('compras', function (Blueprint $table) {
            if (Schema::hasIndex('compras', 'compras_tenant_id_idx')) {
                $table->dropIndex('compras_tenant_id_idx');
            }
            if (Schema::hasIndex('compras', 'compras_proveedor_id_idx')) {
                $table->dropIndex('compras_proveedor_id_idx');
            }
            if (Schema::hasIndex('compras', 'compras_estado_idx')) {
                $table->dropIndex('compras_estado_idx');
            }
            if (Schema::hasIndex('compras', 'compras_fecha_idx')) {
                $table->dropIndex('compras_fecha_idx');
            }
        });

        Schema::table('compra_detalles', function (Blueprint $table) {
            if (Schema::hasIndex('compra_detalles', 'compra_detalles_compra_id_idx')) {
                $table->dropIndex('compra_detalles_compra_id_idx');
            }
            if (Schema::hasIndex('compra_detalles', 'compra_detalles_producto_id_idx')) {
                $table->dropIndex('compra_detalles_producto_id_idx');
            }
            if (Schema::hasIndex('compra_detalles', 'compra_detalles_tenant_id_idx')) {
                $table->dropIndex('compra_detalles_tenant_id_idx');
            }
        });

        Schema::table('cajas', function (Blueprint $table) {
            if (Schema::hasIndex('cajas', 'cajas_tenant_id_idx')) {
                $table->dropIndex('cajas_tenant_id_idx');
            }
            if (Schema::hasIndex('cajas', 'cajas_sucursal_id_idx')) {
                $table->dropIndex('cajas_sucursal_id_idx');
            }
            if (Schema::hasIndex('cajas', 'cajas_activo_idx')) {
                $table->dropIndex('cajas_activo_idx');
            }
            if (Schema::hasIndex('cajas', 'cajas_estado_idx')) {
                $table->dropIndex('cajas_estado_idx');
            }
        });

        Schema::table('sesion_cajas', function (Blueprint $table) {
            if (Schema::hasIndex('sesion_cajas', 'sesion_cajas_caja_id_idx')) {
                $table->dropIndex('sesion_cajas_caja_id_idx');
            }
            if (Schema::hasIndex('sesion_cajas', 'sesion_cajas_user_id_idx')) {
                $table->dropIndex('sesion_cajas_user_id_idx');
            }
            if (Schema::hasIndex('sesion_cajas', 'sesion_cajas_estado_idx')) {
                $table->dropIndex('sesion_cajas_estado_idx');
            }
            if (Schema::hasIndex('sesion_cajas', 'sesion_cajas_tenant_id_idx')) {
                $table->dropIndex('sesion_cajas_tenant_id_idx');
            }
            if (Schema::hasIndex('sesion_cajas', 'sesion_cajas_fecha_apertura_idx')) {
                $table->dropIndex('sesion_cajas_fecha_apertura_idx');
            }
        });

        Schema::table('sucursales', function (Blueprint $table) {
            if (Schema::hasIndex('sucursales', 'sucursales_tenant_id_idx')) {
                $table->dropIndex('sucursales_tenant_id_idx');
            }
            if (Schema::hasIndex('sucursales', 'sucursales_activo_idx')) {
                $table->dropIndex('sucursales_activo_idx');
            }
        });

        Schema::table('almacenes', function (Blueprint $table) {
            if (Schema::hasIndex('almacenes', 'almacenes_tenant_id_idx')) {
                $table->dropIndex('almacenes_tenant_id_idx');
            }
            if (Schema::hasIndex('almacenes', 'almacenes_sucursal_id_idx')) {
                $table->dropIndex('almacenes_sucursal_id_idx');
            }
        });

        Schema::table('almacen_movimientos', function (Blueprint $table) {
            if (Schema::hasIndex('almacen_movimientos', 'almacen_movimientos_tenant_id_idx')) {
                $table->dropIndex('almacen_movimientos_tenant_id_idx');
            }
            if (Schema::hasIndex('almacen_movimientos', 'almacen_movimientos_producto_id_idx')) {
                $table->dropIndex('almacen_movimientos_producto_id_idx');
            }
            if (Schema::hasIndex('almacen_movimientos', 'almacen_movimientos_almacen_id_idx')) {
                $table->dropIndex('almacen_movimientos_almacen_id_idx');
            }
            if (Schema::hasIndex('almacen_movimientos', 'almacen_movimientos_fecha_idx')) {
                $table->dropIndex('almacen_movimientos_fecha_idx');
            }
        });

        Schema::table('ncf_sequences', function (Blueprint $table) {
            if (Schema::hasIndex('ncf_sequences', 'ncf_sequences_tenant_id_idx')) {
                $table->dropIndex('ncf_sequences_tenant_id_idx');
            }
            if (Schema::hasIndex('ncf_sequences', 'ncf_sequences_ncf_tipo_idx')) {
                $table->dropIndex('ncf_sequences_ncf_tipo_idx');
            }
            if (Schema::hasIndex('ncf_sequences', 'ncf_sequences_tipo_comprobante_idx')) {
                $table->dropIndex('ncf_sequences_tipo_comprobante_idx');
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasIndex('roles', 'roles_guard_name_idx')) {
                $table->dropIndex('roles_guard_name_idx');
            }
            if (Schema::hasIndex('roles', 'roles_name_idx')) {
                $table->dropIndex('roles_name_idx');
            }
        });

        Schema::table('permissions', function (Blueprint $table) {
            if (Schema::hasIndex('permissions', 'permissions_guard_name_idx')) {
                $table->dropIndex('permissions_guard_name_idx');
            }
            if (Schema::hasIndex('permissions', 'permissions_name_idx')) {
                $table->dropIndex('permissions_name_idx');
            }
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            if (Schema::hasIndex('model_has_roles', 'model_has_roles_role_id_idx')) {
                $table->dropIndex('model_has_roles_role_id_idx');
            }
            if (Schema::hasIndex('model_has_roles', 'model_has_roles_model_type_idx')) {
                $table->dropIndex('model_has_roles_model_type_idx');
            }
            if (Schema::hasIndex('model_has_roles', 'model_has_roles_model_id_idx')) {
                $table->dropIndex('model_has_roles_model_id_idx');
            }
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            if (Schema::hasIndex('role_has_permissions', 'role_has_permissions_role_id_idx')) {
                $table->dropIndex('role_has_permissions_role_id_idx');
            }
            if (Schema::hasIndex('role_has_permissions', 'role_has_permissions_permission_id_idx')) {
                $table->dropIndex('role_has_permissions_permission_id_idx');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'users_email_idx')) {
                $table->dropIndex('users_email_idx');
            }
            if (Schema::hasIndex('users', 'users_business_instance_id_idx')) {
                $table->dropIndex('users_business_instance_id_idx');
            }
            if (Schema::hasIndex('users', 'users_role_idx')) {
                $table->dropIndex('users_role_idx');
            }
            if (Schema::hasIndex('users', 'users_two_factor_secret_idx')) {
                $table->dropIndex('users_two_factor_secret_idx');
            }
        });
    }
};
