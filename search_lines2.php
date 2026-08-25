<?php
$file = "resources/views/ventas/create.blade.php";
$content = file_get_contents($file);
$lines = explode("\n", $content);
$found = 0;
foreach ($lines as $i => $line) {
    if (strpos($line, "producto_id") !== false || strpos($line, "addLine") !== false || strpos($line, "ci-qty") !== false || strpos($line, "search-results") !== false || strpos($line, "res-item") !== false) {
        echo "Line " . str_pad($i+1, 4) . ": " . substr($line, 0, 120) . "\n";
        $found++;
    }
    if ($found >= 30) break;
}
?>