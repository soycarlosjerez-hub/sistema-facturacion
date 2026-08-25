<?php
$content = file_get_contents('resources/views/owner/business-types/edit.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'facturacion_modo') !== false || strpos($line, 'facturación') !== false || strpos($line, 'Facturación') !== false || strpos($line, 'select') !== false) {
        echo $i . ': ' . substr($line, 0, 120) . PHP_EOL;
    }
}
?>