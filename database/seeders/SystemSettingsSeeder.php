<?php

namespace Database\Seeders;

use App\Models\BusinessInstance;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'clave' => 'empresa_nombre',
                'valor' => 'Erpipo',
                'descripcion' => 'Nombre comercial del establecimiento'
            ],
            [
                'clave' => 'empresa_rnc',
                'valor' => '131-00000-1',
                'descripcion' => 'Registro Nacional de Contribuyente'
            ],
            [
                'clave' => 'empresa_telefono',
                'valor' => '809-000-0000',
                'descripcion' => 'Teléfono de contacto principal'
            ],
            [
                'clave' => 'empresa_direccion',
                'valor' => 'Santo Domingo, República Dominicana',
                'descripcion' => 'Dirección física del negocio'
            ],
            [
                'clave' => 'impuesto_itbis',
                'valor' => '18',
                'descripcion' => 'Porcentaje de ITBIS por defecto'
            ],
            [
                'clave' => 'moneda_simbolo',
                'valor' => 'RD$',
                'descripcion' => 'Símbolo de la moneda local'
            ],
            [
                'clave' => 'sistema_slogan',
                'valor' => 'Calidad y Servicio a tu Alcance',
                'descripcion' => 'Eslogan que aparecerá en facturas'
            ],
        ];

        $mailSettings = [
            [
                'clave' => 'mail_mailer',
                'valor' => 'smtp',
                'descripcion' => 'Controlador de correo (smtp, log, sendmail)'
            ],
            [
                'clave' => 'mail_host',
                'valor' => 'mail.armada.do',
                'descripcion' => 'Servidor SMTP (ej. smtp.gmail.com)'
            ],
            [
                'clave' => 'mail_port',
                'valor' => '465',
                'descripcion' => 'Puerto SMTP (587 TLS, 465 SSL)'
            ],
            [
                'clave' => 'mail_username',
                'valor' => 'no-reply@armada.do',
                'descripcion' => 'Usuario de autenticación SMTP'
            ],
            [
                'clave' => 'mail_password',
                'valor' => Crypt::encryptString('Dn%q#U0tV,65FqSU'),
                'descripcion' => 'Contraseña SMTP (encriptada)'
            ],
            [
                'clave' => 'mail_encryption',
                'valor' => 'ssl',
                'descripcion' => 'Cifrado SMTP (tls, ssl, null)'
            ],
            [
                'clave' => 'mail_from_address',
                'valor' => 'no-reply@armada.do',
                'descripcion' => 'Dirección remitente por defecto'
            ],
            [
                'clave' => 'mail_from_name',
                'valor' => 'Sistema de Facturación',
                'descripcion' => 'Nombre del remitente por defecto'
            ],
        ];

        foreach (array_merge($settings, $mailSettings) as $setting) {
            SystemSetting::updateOrCreate(['clave' => $setting['clave']], $setting);
        }

        $instancias = BusinessInstance::all();
        foreach ($instancias as $instancia) {
            foreach ($mailSettings as $setting) {
                SystemSetting::updateOrCreate(
                    ['clave' => $setting['clave'], 'tenant_id' => $instancia->id],
                    array_merge($setting, ['tenant_id' => $instancia->id])
                );
            }
        }
    }
}
