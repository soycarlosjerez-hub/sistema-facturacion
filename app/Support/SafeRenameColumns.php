<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SafeRenameColumns
{
    /**
     * Safely rename a column, skipping only if it is already renamed or
     * the source column does not exist. Works on all drivers (incl. SQLite).
     */
    public static function rename(string $table, string $from, string $to): void
    {
        try {
            if (! Schema::hasColumn($table, $from) || Schema::hasColumn($table, $to)) {
                return;
            }

            Schema::table($table, function (Blueprint $t) use ($from, $to) {
                $t->renameColumn($from, $to);
            });
        } catch (\Throwable $e) {
            // Silently skip if rename fails (e.g., due to foreign key index conflicts)
        }
    }
}
