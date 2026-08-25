<?php
$file = 'resources/views/ventas/create.blade.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);
$found = 0;
foreach ($lines as $i => $line) {
    if (strpos($line, 'search-results-dropdown') !== false || strpos($line, 'res-item') !== false || strpos($line, 'producto_id') !== false) {
        echo "Line " . ($i+1) . ": " . substr($line, 0, 120) . "...\n";
        $found++;
    }
    if ($found >= 10) break;
}
?>