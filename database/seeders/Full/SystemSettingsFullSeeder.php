<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemSettingsFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `system_settings` (39 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('system_settings');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('system_settings')->truncate();

        $rows = [
            ['id' => 1, 'clave' => 'empresa_nombre', 'grupo' => null, 'valor' => 'Erpipos', 'tipo' => 'string', 'descripcion' => 'Nombre comercial del establecimiento', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-30 14:53:26', 'tenant_id' => null],
            ['id' => 2, 'clave' => 'empresa_rnc', 'grupo' => null, 'valor' => '131-00000-1', 'tipo' => 'string', 'descripcion' => 'Registro Nacional de Contribuyente', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08', 'tenant_id' => null],
            ['id' => 3, 'clave' => 'empresa_telefono', 'grupo' => null, 'valor' => '809-000-0000', 'tipo' => 'string', 'descripcion' => 'Teléfono de contacto principal', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08', 'tenant_id' => null],
            ['id' => 4, 'clave' => 'empresa_direccion', 'grupo' => null, 'valor' => 'Santo Domingo, República Dominicana', 'tipo' => 'string', 'descripcion' => 'Dirección física del negocio', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08', 'tenant_id' => null],
            ['id' => 5, 'clave' => 'impuesto_itbis', 'grupo' => null, 'valor' => '18', 'tipo' => 'string', 'descripcion' => 'Porcentaje de ITBIS por defecto', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08', 'tenant_id' => null],
            ['id' => 6, 'clave' => 'moneda_simbolo', 'grupo' => null, 'valor' => 'RD$', 'tipo' => 'string', 'descripcion' => 'Símbolo de la moneda local', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08', 'tenant_id' => null],
            ['id' => 7, 'clave' => 'sistema_slogan', 'grupo' => null, 'valor' => 'Calidad y Servicio a tu Alcance', 'tipo' => 'string', 'descripcion' => 'Eslogan que aparecerá en facturas', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08', 'tenant_id' => null],
            ['id' => 8, 'clave' => 'mail_mailer', 'grupo' => null, 'valor' => 'smtp', 'tipo' => 'string', 'descripcion' => 'Controlador de correo (smtp, log, sendmail)', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-09 12:42:24', 'tenant_id' => null],
            ['id' => 9, 'clave' => 'mail_host', 'grupo' => null, 'valor' => 'mail.armada.do', 'tipo' => 'string', 'descripcion' => 'Servidor SMTP (ej. smtp.gmail.com)', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-09 12:42:24', 'tenant_id' => null],
            ['id' => 10, 'clave' => 'mail_port', 'grupo' => null, 'valor' => '465', 'tipo' => 'string', 'descripcion' => 'Puerto SMTP (587 TLS, 465 SSL)', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-09 12:42:24', 'tenant_id' => null],
            ['id' => 11, 'clave' => 'mail_username', 'grupo' => null, 'valor' => 'no-reply@armada.do', 'tipo' => 'string', 'descripcion' => 'Usuario de autenticación SMTP', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-09 12:42:24', 'tenant_id' => null],
            ['id' => 12, 'clave' => 'mail_password', 'grupo' => null, 'valor' => 'eyJpdiI6IndVdDdEZWNjb3Mrcjl3NlA2WHlKZ3c9PSIsInZhbHVlIjoiKzR5a1A5aTZYeU9xV1RLMFJGcXRDNVpWTVpLYkdHUjJlc3dmNklQdndWTT0iLCJtYWMiOiJhNmZlMTczMWVjZmE3ZDE5ODQwMWU0YmQ2NDdjNDc5NDQ0ZjcwMzliODMyNWMyYjQ2MmIzZjNjYjIyYTFhNWQwIiwidGFnIjoiIn0=', 'tipo' => 'string', 'descripcion' => 'Contraseña SMTP (encriptada)', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-28 00:03:39', 'tenant_id' => null],
            ['id' => 13, 'clave' => 'mail_encryption', 'grupo' => null, 'valor' => 'ssl', 'tipo' => 'string', 'descripcion' => 'Cifrado SMTP (tls, ssl, null)', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-09 12:42:24', 'tenant_id' => null],
            ['id' => 14, 'clave' => 'mail_from_address', 'grupo' => null, 'valor' => 'no-reply@armada.do', 'tipo' => 'string', 'descripcion' => 'Dirección remitente por defecto', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-09 12:42:24', 'tenant_id' => null],
            ['id' => 15, 'clave' => 'mail_from_name', 'grupo' => null, 'valor' => 'Sistema de Facturación', 'tipo' => 'string', 'descripcion' => 'Nombre del remitente por defecto', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08', 'tenant_id' => null],
            ['id' => 16, 'clave' => 'empresa_nombre', 'grupo' => null, 'valor' => 'Erpipos', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-07-01 15:09:59', 'updated_at' => '2026-07-30 14:53:26', 'tenant_id' => 1],
            ['id' => 17, 'clave' => 'empresa_telefono', 'grupo' => null, 'valor' => '', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-07-01 15:09:59', 'updated_at' => '2026-07-01 15:09:59', 'tenant_id' => 1],
            ['id' => 18, 'clave' => 'moneda_simbolo', 'grupo' => null, 'valor' => 'RD$', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-07-01 15:09:59', 'updated_at' => '2026-07-01 15:09:59', 'tenant_id' => 1],
            ['id' => 19, 'clave' => 'impuesto_itbis', 'grupo' => null, 'valor' => '18', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-07-01 15:09:59', 'updated_at' => '2026-07-01 15:09:59', 'tenant_id' => 1],
            ['id' => 20, 'clave' => 'empresa_nombre', 'grupo' => null, 'valor' => 'Michelle Casero y Gourmet', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-07-02 20:33:20', 'updated_at' => '2026-08-13 23:50:13', 'tenant_id' => 2],
            ['id' => 21, 'clave' => 'empresa_telefono', 'grupo' => null, 'valor' => '809-348-4259', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-07-02 20:33:20', 'updated_at' => '2026-07-02 20:33:20', 'tenant_id' => 2],
            ['id' => 22, 'clave' => 'moneda_simbolo', 'grupo' => null, 'valor' => 'RD$', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-07-02 20:33:21', 'updated_at' => '2026-07-02 20:33:21', 'tenant_id' => 2],
            ['id' => 23, 'clave' => 'impuesto_itbis', 'grupo' => null, 'valor' => '18', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-07-02 20:33:21', 'updated_at' => '2026-07-02 20:33:21', 'tenant_id' => 2],
            ['id' => 64, 'clave' => 'empresa_nombre', 'grupo' => null, 'valor' => 'Erpipos', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-07-23 19:37:48', 'updated_at' => '2026-07-30 14:53:26', 'tenant_id' => 5],
            ['id' => 65, 'clave' => 'error_alert_email', 'grupo' => null, 'valor' => 'jcjerez@gmail.com', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-07-27 23:51:31', 'updated_at' => '2026-07-27 23:51:31', 'tenant_id' => null],
            ['id' => 66, 'clave' => 'empresa_nombre', 'grupo' => null, 'valor' => 'Colmado rodriguez', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-12 14:54:49', 'updated_at' => '2026-08-12 14:54:49', 'tenant_id' => 7],
            ['id' => 67, 'clave' => 'empresa_telefono', 'grupo' => null, 'valor' => '8295813110', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-12 14:54:49', 'updated_at' => '2026-08-12 14:54:49', 'tenant_id' => 7],
            ['id' => 68, 'clave' => 'moneda_simbolo', 'grupo' => null, 'valor' => 'RD$', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-12 14:54:49', 'updated_at' => '2026-08-12 14:54:49', 'tenant_id' => 7],
            ['id' => 69, 'clave' => 'impuesto_itbis', 'grupo' => null, 'valor' => '0', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-12 14:54:49', 'updated_at' => '2026-08-13 12:19:15', 'tenant_id' => 7],
            ['id' => 70, 'clave' => 'empresa_rnc', 'grupo' => null, 'valor' => '1-33-75781-8', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-13 23:54:44', 'updated_at' => '2026-08-13 23:54:44', 'tenant_id' => 2],
            ['id' => 71, 'clave' => 'sistema_slogan', 'grupo' => null, 'valor' => 'Comida criolla como en casa', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-13 23:54:44', 'updated_at' => '2026-08-13 23:54:44', 'tenant_id' => 2],
            ['id' => 72, 'clave' => 'empresa_direccion', 'grupo' => null, 'valor' => 'Calle Santiago esq, C. García Godoy 54, Santo Domingo 10209', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-13 23:54:44', 'updated_at' => '2026-08-13 23:54:44', 'tenant_id' => 2],
            ['id' => 73, 'clave' => 'empresa_nombre', 'grupo' => null, 'valor' => 'Arte', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-14 17:35:34', 'updated_at' => '2026-08-14 17:35:34', 'tenant_id' => 9],
            ['id' => 74, 'clave' => 'empresa_telefono', 'grupo' => null, 'valor' => '8097507255', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-14 17:35:34', 'updated_at' => '2026-08-14 17:35:34', 'tenant_id' => 9],
            ['id' => 75, 'clave' => 'empresa_rnc', 'grupo' => null, 'valor' => '0232152', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-14 17:35:34', 'updated_at' => '2026-08-14 17:35:34', 'tenant_id' => 9],
            ['id' => 76, 'clave' => 'empresa_direccion', 'grupo' => null, 'valor' => 'Calle Maximo Gomez', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-14 17:35:34', 'updated_at' => '2026-08-14 17:35:34', 'tenant_id' => 9],
            ['id' => 77, 'clave' => 'empresa_email', 'grupo' => null, 'valor' => 'soycarlosjerez@gmail.com', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-14 17:35:34', 'updated_at' => '2026-08-14 17:35:34', 'tenant_id' => 9],
            ['id' => 78, 'clave' => 'moneda_simbolo', 'grupo' => null, 'valor' => 'RD$', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-14 17:35:34', 'updated_at' => '2026-08-14 17:35:34', 'tenant_id' => 9],
            ['id' => 79, 'clave' => 'impuesto_itbis', 'grupo' => null, 'valor' => '18', 'tipo' => 'string', 'descripcion' => null, 'created_at' => '2026-08-14 17:35:34', 'updated_at' => '2026-08-14 17:35:34', 'tenant_id' => 9],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('system_settings')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
