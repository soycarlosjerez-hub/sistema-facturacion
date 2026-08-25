<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'data-action') !== false && strpos($line, 'add') !== false && strpos($line, 'click') !== false) {
        echo $i . ': ' . substr($line, 0, 120) . PHP_EOL;
    }
    if (strpos($line, 'addEventListener') !== false && (strpos($line, 'add') !== false || strpos($line, 'click') !== false)) {
        echo $i . ': ' . substr($line, 0, 120) . PHP_EOL;
    }
    if (strpos($line, 'addToCart') !== false && strpos($line, 'function') !== false) {
        echo $i . ': ' . substr($line, 0, 120) . PHP_EOL;
    }
}
?>