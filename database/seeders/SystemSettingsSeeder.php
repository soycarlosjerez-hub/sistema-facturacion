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
                'key' => 'empresa_nombre',
                'value' => 'Colmado Premium',
                'description' => 'Nombre comercial del establecimiento'
            ],
            [
                'key' => 'empresa_rnc',
                'value' => '131-00000-1',
                'description' => 'Registro Nacional de Contribuyente'
            ],
            [
                'key' => 'empresa_telefono',
                'value' => '809-000-0000',
                'description' => 'Teléfono de contacto principal'
            ],
            [
                'key' => 'empresa_direccion',
                'value' => 'Santo Domingo, República Dominicana',
                'description' => 'Dirección física del negocio'
            ],
            [
                'key' => 'impuesto_itbis',
                'value' => '18',
                'description' => 'Porcentaje de ITBIS por defecto'
            ],
            [
                'key' => 'moneda_simbolo',
                'value' => 'RD$',
                'description' => 'Símbolo de la moneda local'
            ],
            [
                'key' => 'sistema_slogan',
                'value' => 'Calidad y Servicio a tu Alcance',
                'description' => 'Eslogan que aparecerá en facturas'
            ],
        ];

        $mailSettings = [
            [
                'key' => 'mail_mailer',
                'value' => 'smtp',
                'description' => 'Controlador de correo (smtp, log, sendmail)'
            ],
            [
                'key' => 'mail_host',
                'value' => 'mail.armada.do',
                'description' => 'Servidor SMTP (ej. smtp.gmail.com)'
            ],
            [
                'key' => 'mail_port',
                'value' => '465',
                'description' => 'Puerto SMTP (587 TLS, 465 SSL)'
            ],
            [
                'key' => 'mail_username',
                'value' => 'no-reply@armada.do',
                'description' => 'Usuario de autenticación SMTP'
            ],
            [
                'key' => 'mail_password',
                'value' => Crypt::encryptString('Dn%q#U0tV,65FqSU'),
                'description' => 'Contraseña SMTP (encriptada)'
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'ssl',
                'description' => 'Cifrado SMTP (tls, ssl, null)'
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'no-reply@armada.do',
                'description' => 'Dirección remitente por defecto'
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'Sistema de Facturación',
                'description' => 'Nombre del remitente por defecto'
            ],
        ];

        foreach (array_merge($settings, $mailSettings) as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        $instancias = BusinessInstance::all();
        foreach ($instancias as $instancia) {
            foreach ($mailSettings as $setting) {
                SystemSetting::updateOrCreate(
                    ['key' => $setting['key'], 'tenant_id' => $instancia->id],
                    array_merge($setting, ['tenant_id' => $instancia->id])
                );
            }
        }
    }
}
