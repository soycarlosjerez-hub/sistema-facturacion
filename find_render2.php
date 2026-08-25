<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'renderProductos') !== false && strpos($line, 'function') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
    if (strpos($line, 'function render') !== false && strpos($line, 'Productos') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
}
?>