<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'pos-tabs') !== false && strpos($line, 'class') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
    if (strpos($line, 'pos-products') !== false && strpos($line, 'class') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
    if (strpos($line, 'productos') !== false && strpos($line, 'data-tab') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
}
?>