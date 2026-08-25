<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'data-action') !== false && (strpos($line, 'add') !== false || strpos($line, 'onclick') !== false)) {
        echo $i . ': ' . substr($line, 0, 120) . PHP_EOL;
    }
}
?>