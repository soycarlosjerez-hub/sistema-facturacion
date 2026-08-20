<?php
// Mark SGC migrations as applied (tables already exist)
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$migrations = [
    '2026_08_20_105000_create_evaluaciones_proveedores_table.php',
    '2026_08_20_105100_create_evaluaciones_proveedores_documentos_table.php',
    '2026_08_20_105200_create_incumplimientos_proveedores_table.php',
    '2026_08_20_105300_create_evaluaciones_periodicas_proveedores_table.php',
    '2026_08_20_106000_create_auditorias_internas_table.php',
    '2026_08_20_106100_create_programas_auditoria_table.php',
    '2026_08_20_106200_create_checklist_auditorias_table.php',
    '2026_08_20_106300_create_hallazgos_auditoria_table.php',
    '2026_08_20_107000_create_revisiones_direccion_table.php',
    '2026_08_20_107100_create_asistentes_revisiones_direccion_table.php',
    '2026_08_20_107200_create_revisiones_direccion_entradas_table.php',
    '2026_08_20_107300_create_revisiones_direccion_salidas_table.php',
    '2026_08_20_108000_create_no_conformidades_table.php',
    '2026_08_20_108100_create_analisis_causas_table.php',
    '2026_08_20_108200_create_acciones_correctivas_table.php',
    '2026_08_20_108300_create_verificaciones_accion_table.php',
    '2026_08_20_109000_create_mejoras_continuas_table.php',
    '2026_08_20_109100_create_propuestas_mejora_table.php',
    '2026_08_20_109200_fix_cross_references_sgc.php',
];

$batch = DB::table('migrations')->max('batch') ?: 0;
$count = 0;

foreach ($migrations as $mig) {
    DB::table('migrations')->insert([
        'migration' => $mig,
        'batch' => $batch + 1
    ]);
    echo "Marked: $mig\n";
    $count++;
}

echo "\nTotal marked: $count\n";
