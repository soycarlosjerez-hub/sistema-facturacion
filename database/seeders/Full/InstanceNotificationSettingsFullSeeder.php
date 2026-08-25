<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstanceNotificationSettingsFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `instance_notification_settings` (3 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('instance_notification_settings');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('instance_notification_settings')->truncate();

        $rows = [
            ['id' => 1, 'business_instance_id' => 2, 'enabled' => 1, 'sale_created' => 1, 'sale_paid' => 1, 'sale_cancelled' => 1, 'order_confirmed' => 1, 'order_ready' => 1, 'order_shipped' => 1, 'payment_received' => 1, 'credit_overdue' => 1, 'credit_abono' => 1, 'stock_critical' => 1, 'stock_restocked' => 0, 'product_created' => 0, 'shift_opened' => 1, 'shift_closed' => 1, 'cash_shortage' => 1, 'daily_report' => 0, 'ncff_expiring' => 1, 'ecf_certificate_expiring' => 1, 'backup_completed' => 0, 'backup_failed' => 1, 'user_registered' => 1, 'created_at' => '2026-08-03 22:46:44', 'updated_at' => '2026-08-03 22:46:44'],
            ['id' => 2, 'business_instance_id' => 8, 'enabled' => 1, 'sale_created' => 1, 'sale_paid' => 1, 'sale_cancelled' => 1, 'order_confirmed' => 1, 'order_ready' => 1, 'order_shipped' => 1, 'payment_received' => 1, 'credit_overdue' => 1, 'credit_abono' => 1, 'stock_critical' => 1, 'stock_restocked' => 0, 'product_created' => 0, 'shift_opened' => 1, 'shift_closed' => 1, 'cash_shortage' => 1, 'daily_report' => 0, 'ncff_expiring' => 1, 'ecf_certificate_expiring' => 1, 'backup_completed' => 0, 'backup_failed' => 1, 'user_registered' => 1, 'created_at' => '2026-08-04 11:26:01', 'updated_at' => '2026-08-04 11:26:01'],
            ['id' => 3, 'business_instance_id' => 9, 'enabled' => 1, 'sale_created' => 1, 'sale_paid' => 1, 'sale_cancelled' => 1, 'order_confirmed' => 1, 'order_ready' => 1, 'order_shipped' => 1, 'payment_received' => 1, 'credit_overdue' => 1, 'credit_abono' => 1, 'stock_critical' => 1, 'stock_restocked' => 0, 'product_created' => 0, 'shift_opened' => 1, 'shift_closed' => 1, 'cash_shortage' => 1, 'daily_report' => 0, 'ncff_expiring' => 1, 'ecf_certificate_expiring' => 1, 'backup_completed' => 0, 'backup_failed' => 1, 'user_registered' => 1, 'created_at' => '2026-08-14 18:45:25', 'updated_at' => '2026-08-14 18:45:25'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('instance_notification_settings')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
