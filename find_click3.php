<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'data-action') !== false && strpos($line, 'addEventListener') !== false) {
        echo $i . ': ' . substr($line, 0, 120) . PHP_EOL;
    }
    if (strpos($line, 'data-action') !== false && strpos($line, 'click') !== false) {
        echo $i . ': ' . substr($line, 0, 120) . PHP_EOL;
    }
    if (strpos($line, 'querySelectorAll') !== false && strpos($line, 'data-action') !== false) {
        echo $i . ': ' . substr($line, 0, 120) . PHP_EOL;
    }
}
?>