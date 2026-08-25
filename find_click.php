<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'pos-tab') !== false && strpos($line, 'click') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
    if (strpos($line, 'addEventListener') !== false && (strpos($line, 'pos-tab') !== false || strpos($line, 'pos-tab') !== false)) {
        echo $i . ': ' . $line . PHP_EOL;
    }
    if (strpos($line, 'querySelectorAll') !== false && strpos($line, 'pos-tab') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
}
?>