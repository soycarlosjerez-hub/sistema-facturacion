<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SQLite-safe helpers for migrations that need MySQL-only operations.
 *
 * SQLite has severe limitations:
 * - No MODIFY COLUMN / CHANGE COLUMN
 * - No RENAME COLUMN (without table recreation, which fails on FK indexes)
 * - No ADD INDEX on existing column
 * - No DROP COLUMN (without table recreation)
 * - No information_schema.COLUMNS
 *
 * These helpers either skip silently on non-MySQL or provide a fallback
 * that works across both SQLite and MySQL.
 */
class SafeAlterTable
{
    /**
     * Safely run an ALTER statement, skipping on non-MySQL.
     * SQLite recreates tables on ALTER, which fails on FK index conflicts.
     */
    public static function alter(string $sql): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        try {
            DB::statement($sql);
        } catch (\Throwable) {
            // Silently skip - column already altered or incompatible type
        }
    }

    /**
     * Check if a column exists on a table (SQLite-safe).
     */
    public static function hasColumn(string $table, string $column): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            return in_array(
                $column,
                DB::getSchemaBuilder()->getColumnListing($table)
            );
        }

        $result = DB::select(
            'SELECT COUNT(*) as count FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        );

        return ($result[0]->count ?? 0) > 0;
    }

    /**
     * Safely change a column type (MySQL-only).
     * SQLite: silently skip.
     */
    public static function changeColumn(string $table, string $column, string $sql): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        try {
            DB::statement("ALTER TABLE {$table} {$sql}");
        } catch (\Throwable) {
            // Column already has the correct type or incompatible
        }
    }

    /**
     * Safely drop a column (MySQL-only).
     * SQLite: silently skip (no DROP COLUMN without recreation).
     */
    public static function dropColumn(string $table, string $column): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        try {
            DB::statement("ALTER TABLE {$table} DROP COLUMN {$column}");
        } catch (\Throwable) {
            // Column doesn't exist or can't be dropped
        }
    }
}
