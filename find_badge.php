<?php
$content = file_get_contents('resources/views/ventas/create.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'count-servicios') !== false || strpos($line, 'count-services') !== false || strpos($line, 'count-serv') !== false || strpos($line, 'badge-count') !== false) {
        echo $i . ': ' . $line . PHP_EOL;
    }
}
?>