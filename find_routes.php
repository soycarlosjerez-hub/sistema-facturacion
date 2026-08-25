<?php
$content = file_get_contents('routes/web.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'buscarEquipo') !== false || strpos($line, 'buscarProducto') !== false || strpos($line, 'ventas/buscar') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
}
?>