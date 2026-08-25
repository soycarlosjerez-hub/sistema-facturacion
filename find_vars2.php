<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'let productos') !== false && strpos($line, '=') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
    if (strpos($line, 'const productos') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
    if (strpos($line, 'let cart') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
    if (strpos($line, 'const cart') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
}
?>