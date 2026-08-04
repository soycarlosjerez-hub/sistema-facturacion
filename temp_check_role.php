<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$r = \App\Models\InstanceRole::find(13);
if (!$r) {
    echo "Role 13 no encontrado\n";
    exit(1);
}

echo "Role: " . $r->name . "\n";
echo "Instance: " . $r->businessInstance->nombre . "\n";
echo "Instance ID: " . $r->business_instance_id . "\n";
echo "\nTodos los módulos:\n";
foreach ($r->modules as $m) {
    echo "  - {$m->modulo_key} (visible: " . ($m->is_visible ? 'SI' : 'NO') . ")\n";
}
echo "\nSolo visibles:\n";
foreach ($r->visibleModules as $m) {
    echo "  - {$m->modulo_key}\n";
}

// Verificar si tiene contabilidad
$contabilidadMods = ['ncf','ecf','secuencias-ecf','certificados-digitales','libros-ventas','libros-compras','reportes-retenciones','reportes-fiscales','reportes-resumen','formulario-14-14'];
$tiene = [];
foreach ($contabilidadMods as $key) {
    $found = $r->modules->first(fn($m) => $m->modulo_key === $key);
    if ($found) {
        $tiene[] = $key . " (" . ($found->is_visible ? 'VISIBLE' : 'OCULTO') . ")";
    }
}
echo "\nMódulos de contabilidad encontrados: " . (count($tiene) > 0 ? implode(', ', $tiene) : 'NINGUNO') . "\n";

// Verificar todos los módulos disponibles en la BD
echo "\nTodos los módulos activos en la BD:\n";
foreach (\App\Models\Modulo::allActive() as $mod) {
    $assigned = $r->modules->first(fn($m) => $m->modulo_key === $mod->key);
    $marker = $assigned ? ($assigned->is_visible ? '✓' : '~') : '✗';
    echo "  {$marker} {$mod->key} ({$mod->categoria})\n";
}
