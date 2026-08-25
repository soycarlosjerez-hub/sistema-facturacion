<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'getFilteredProducts') !== false || strpos($line, 'triggerSearch') !== false || (strpos($line, 'serviciosPre') !== false && strpos($line, 'productosPre') !== false)) {
        echo $i . ': ' . substr($line, 0, 150) . PHP_EOL;
    }
}
?>