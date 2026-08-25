<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'function filterProductos') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
}
?>