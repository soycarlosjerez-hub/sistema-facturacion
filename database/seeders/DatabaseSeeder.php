<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Snapshot completo de la base de datos generado desde solo_inserts.sql
     * (65 tablas, 11,851 filas). Cada seeder hace truncate + insert en chunks.
     * Se ejecutan en orden de dependencias FK y con FK checks deshabilitadas.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        
        // Estado de migraciones del entorno origen
        $this->call(Full\MigrationsFullSeeder::class); // 261 filas
        $this->call(Full\CacheFullSeeder::class); // 10 filas
        $this->call(Full\JobsFullSeeder::class); // 122 filas
        $this->call(Full\SessionsFullSeeder::class); // 11 filas
        $this->call(Full\PasswordResetTokensFullSeeder::class); // 1 filas
        $this->call(Full\PersonalAccessTokensFullSeeder::class); // 2 filas
        $this->call(Full\ClientApiTokensFullSeeder::class); // 2 filas
        $this->call(Full\InstanceApiKeysFullSeeder::class); // 1 filas
        $this->call(Full\BackupsFullSeeder::class); // 2 filas

        // Catálogos base
        $this->call(Full\PlansFullSeeder::class); // 4 filas
        $this->call(Full\BusinessTypesFullSeeder::class); // 11 filas
        $this->call(BusinessTypeMixtoSeeder::class); // Modo mixto: productos + servicios
        $this->call(Full\ModulosFullSeeder::class); // 86 filas
        $this->call(Full\PermissionsFullSeeder::class); // 218 filas
        $this->call(Full\WizardStepsFullSeeder::class); // 14 filas
        $this->call(Full\SystemSettingsFullSeeder::class); // 39 filas
        $this->call(Full\TiposVentasFullSeeder::class); // 12 filas
        $this->call(Full\TiposComprasFullSeeder::class); // 9 filas
        $this->call(Full\DeliveryCompaniesFullSeeder::class); // 6 filas
        $this->call(Full\MesaUbicacionesFullSeeder::class); // 4 filas
        $this->call(Full\MesaCategoriasFullSeeder::class); // 3 filas

        // Usuarios y seguridad
        $this->call(Full\UsersFullSeeder::class); // 26 filas
        $this->call(Full\RolesFullSeeder::class); // 13 filas

        // Instancias (negocios) multi-tenant
        $this->call(Full\BusinessInstancesFullSeeder::class); // 8 filas

        // Pivotes de roles y permisos
        $this->call(Full\ModelHasRolesFullSeeder::class); // 25 filas
        $this->call(Full\RoleHasPermissionsFullSeeder::class); // 905 filas

        // Relaciones de módulos
        $this->call(Full\BusinessTypeModulesFullSeeder::class); // 439 filas
        $this->call(Full\InstanceRolesFullSeeder::class); // 15 filas
        $this->call(Full\InstanceNotificationSettingsFullSeeder::class); // 3 filas
        $this->call(Full\PagosInstanciaFullSeeder::class); // 2 filas

        // Estructura operativa
        $this->call(Full\SucursalesFullSeeder::class); // 5 filas
        $this->call(Full\AlmacenesFullSeeder::class); // 3 filas
        $this->call(Full\CuentasBancariasFullSeeder::class); // 2 filas
        $this->call(Full\ImpresorasFullSeeder::class); // 1 filas

        // Maestros de inventario
        $this->call(Full\CategoriasFullSeeder::class); // 18 filas
        $this->call(Full\ProductosFullSeeder::class); // 217 filas
        $this->call(Full\ClientesFullSeeder::class); // 50 filas
        $this->call(Full\ProveedoresFullSeeder::class); // 14 filas
        $this->call(MarcaTecnologicaSeeder::class); // Marcas tecnológicas globales (idempotente)

        // Secuencias fiscales (DGII)
        $this->call(Full\NcfSequencesFullSeeder::class); // 39 filas
        $this->call(Full\SecuenciasEcfFullSeeder::class); // 50 filas

        // Listas de precio
        $this->call(Full\ListaPreciosFullSeeder::class); // 1 filas
        $this->call(Full\ListaPrecioItemsFullSeeder::class); // 5 filas
        $this->call(Full\ListaPrecioLogsFullSeeder::class); // 1 filas

        // Operación: cajas y mesas
        $this->call(Full\CajasFullSeeder::class); // 8 filas
        $this->call(Full\MesasFullSeeder::class); // 13 filas

        // Órdenes restaurante
        $this->call(Full\OrdenesFullSeeder::class); // 4 filas
        $this->call(Full\OrdenDetallesFullSeeder::class); // 5 filas
        $this->call(Full\ReservacionesFullSeeder::class); // 2 filas

        // Delivery
        $this->call(Full\DeliveryZonesFullSeeder::class); // 1 filas
        $this->call(Full\DeliveryDriversFullSeeder::class); // 1 filas

        // Transacciones
        $this->call(Full\VentasFullSeeder::class); // 69 filas
        $this->call(Full\VentaDetallesFullSeeder::class); // 76 filas
        $this->call(Full\ComprasFullSeeder::class); // 1 filas
        $this->call(Full\CompraDetallesFullSeeder::class); // 1 filas
        $this->call(Full\PagosFullSeeder::class); // 46 filas
        $this->call(Full\GastosFullSeeder::class); // 68 filas
        $this->call(Full\SesionCajasFullSeeder::class); // 14 filas
        $this->call(Full\AlmacenMovimientosFullSeeder::class); // 38 filas

        // Facturación electrónica
        $this->call(Full\EcfDocumentosFullSeeder::class); // 3 filas
        $this->call(Full\EcfLogEnviosFullSeeder::class); // 10 filas

        // Impresión
        $this->call(Full\HistorialImpresionFullSeeder::class); // 13 filas

        // Configuración por instancia
        $this->call(Full\InstanceRoleModulesFullSeeder::class); // 296 filas

        // Logs y auditoría
        $this->call(Full\AuditLogsFullSeeder::class); // 5954 filas
        $this->call(Full\UserActivityLogsFullSeeder::class); // 1393 filas
        $this->call(Full\ApiRequestLogsFullSeeder::class); // 822 filas
        $this->call(Full\InstanceErrorLogsFullSeeder::class); // 353 filas

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
