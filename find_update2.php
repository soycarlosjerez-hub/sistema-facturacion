<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'count-all') !== false || strpos($line, 'count-servicios') !== false || strpos($line, 'count-equipos') !== false || strpos($line, 'count-all') !== false) {
        if (strpos($line, 'getElementById') !== false || strpos($line, 'innerText') !== false || strpos($line, 'textContent') !== false || strpos($line, '= ') !== false) {
            echo $i . ': ' . substr($line, 0, 150) . PHP_EOL;
        }
    }
}
?>