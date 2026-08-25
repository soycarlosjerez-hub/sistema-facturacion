<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SessionsFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `sessions` (11 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('sessions');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('sessions')->truncate();

        $rows = [
            ['id' => '22RgPTq1QTLSaILfITAI0mWhZyTf61Zxt0w5fU1r', 'user_id' => null, 'ip_address' => '172.70.226.138', 'user_agent' => 'WhatsApp/2.23.20.0', 'payload' => 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTW91UXFzNWVCY2xuTWtEWWZXMXB1QWV4bUhlY1ZmNXE4amVWbkcwNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZXJwaXBvcy5hcm1hZGEuZG8vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 'last_activity' => 1787070516],
            ['id' => '24Y97hHK3lb4j5PZvgmJqLC03uzigkMplO2gzrNi', 'user_id' => 23, 'ip_address' => '108.162.210.163', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'payload' => 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiR0RhNGZmSTNYMm5wckx6emNROGZ3Y0RhMTYyTTFlR3RoWkRIZ1BXTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTI6Imh0dHBzOi8vZXJwaXBvcy5hcm1hZGEuZG8vYXBpL25vdGlmaWNhdGlvbnMvcmVjZW50LzUiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyMztzOjExOiJzdWN1cnNhbF9pZCI7aToyO3M6MTg6ImJ1c2luZXNzX3R5cGVfc2x1ZyI7czoxMToicmVzdGF1cmFudGUiO3M6MjA6ImJ1c2luZXNzX2luc3RhbmNlX2lkIjtpOjI7fQ==', 'last_activity' => 1787075330],
            ['id' => 'Cq0x6cybC9jPdZ57vdNuuMdlzycpVqglwqkN2waC', 'user_id' => null, 'ip_address' => '172.70.226.138', 'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'payload' => 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUVByekppSEFGRUg0QXdjYmJ4NjRtOWpPZ0JpNnRna2pRd2dhOGZ0TCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vZXJwaXBvcy5hcm1hZGEuZG8vcmVnaXN0ZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 'last_activity' => 1787070507],
            ['id' => 'gPyT7IRq73hcwpkDu1vgB6otGqrq0QUKzxNi5uPq', 'user_id' => null, 'ip_address' => '103.203.57.3', 'user_agent' => 'Mozilla/5.0 zgrab/0.x', 'payload' => 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTjdOOXJJWXpTcHhHd2RSMWFEa3hnSHNtOUtFQ3ZCVWV6WEU5RnNzbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly8xNDcuOTMuNi4xMTIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 'last_activity' => 1787070262],
            ['id' => 'LBcXtQnHBpzkmqnr6jGqekaL6vV2n7VpopSpfWHX', 'user_id' => null, 'ip_address' => '8.209.115.19', 'user_agent' => 'curl/7.74.0', 'payload' => 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ1JlRVcwbmFpdXhlbHJtT3Y0Nm5WQ21SMDFLNzRVWXBFbWMwNGdIMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly8xNDcuOTMuNi4xMTIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 'last_activity' => 1787071774],
            ['id' => 'MFnFegYHUCJBzfSiMDrIyXVm7QLSLLURuJEPlSdr', 'user_id' => null, 'ip_address' => '123.160.223.75', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'payload' => 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRUl2anlFQXdoVEVwSlVhQzhKeU1YZTZxb0hEb2h4ZHQ0VHhqaVlkMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly8xNDcuOTMuNi4xMTIvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 'last_activity' => 1787071878],
            ['id' => 'mlAi4evwA7sNpmeFwKgacw9vTx97pm8qw1xDWHVj', 'user_id' => null, 'ip_address' => '8.209.115.19', 'user_agent' => 'curl/7.74.0', 'payload' => 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQVBkaTU5Y0d3bEFpRVZVbHBMblZXQmJHakt6OUM3Z281dWxhYmZreSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly8xNDcuOTMuNi4xMTIvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 'last_activity' => 1787071774],
            ['id' => 'nrBI4qD7w03vx34ywWI9jw2t7Gsb2VVLyIadUaPE', 'user_id' => null, 'ip_address' => '164.90.223.245', 'user_agent' => 'Mozilla/5.0', 'payload' => 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiclczb2tpZ01FMEFmN25sQU5ISUNHYVdHcHhWR2NRZm80eGxxZHQ4dCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6OTI6Imh0dHBzOi8vdm1pMzM3ODg4NS5jb250YWJvc2VydmVyLm5ldC9yZWdpc3Rlcj9lbWFpbCU1QjAlNUQ9eCZuYW1lJTVCMCU1RD14JnVzZXJuYW1lJTVCMCU1RD14Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo5OToiaHR0cHM6Ly92bWkzMzc4ODg1LmNvbnRhYm9zZXJ2ZXIubmV0L3NlYXJjaD9jYXRlZ29yeSU1QjAlNUQ9eCZxJTVCMCU1RD14JnNvcnQlNUIwJTVEPXgmdGFnJTVCMCU1RD14Ijt9fQ==', 'last_activity' => 1787071881],
            ['id' => 'Pfdbb8aAixZ6Tepka9DKcnQxEBnkdMj2vMUvmr3V', 'user_id' => 8, 'ip_address' => '104.23.248.134', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'payload' => 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiak55TENRRTEzSVduYTI5dnRSSm1wVW16TXhXVjNrUzBXTk9GbjZQSCI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo1NDoiaHR0cHM6Ly9lcnBpcG9zLm1pY2FzZXJvZ291LmNvbS9yZXN0YXVyYW50ZS9rZHMvb3JkZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxODoiYnVzaW5lc3NfdHlwZV9zbHVnIjtzOjExOiJyZXN0YXVyYW50ZSI7fQ==', 'last_activity' => 1787076149],
            ['id' => 'Xxq4Ah5VpKzqJWafVgEFMg3iMV62zutMu00IIhZa', 'user_id' => null, 'ip_address' => '172.70.226.138', 'user_agent' => 'WhatsApp/2.23.20.0', 'payload' => 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTWNCOE4zalc2cVFCV2tZOEhwaTVyVFZhZlZBc0hiSURqaWw4OUd6eCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vZXJwaXBvcy5hcm1hZGEuZG8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 'last_activity' => 1787070516],
            ['id' => 'ZLwgcElHaVUvvjDG4tXh5fFnZcyGY1tTSs01aYAg', 'user_id' => null, 'ip_address' => '185.12.59.118', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:132.0) Gecko/20100101 Firefox/132.0', 'payload' => 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTWN2ZmdXR3R2Q0hzRUJHWTR1bFdJM0hTTTM3NzNEZXBxREFpR2l5WCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vMTQ3LjkzLjYuMTEyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 'last_activity' => 1787074643],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('sessions')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
