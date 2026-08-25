<?php
$file = "resources/views/ventas/create.blade.php";
$content = file_get_contents($file);
$lines = explode("\n", $content);
$found = 0;
foreach ($lines as $i => $line) {
    if (strpos($line, "new Producto") !== false || strpos($line, "agregar") !== false || strpos($line, "addLine") !== false || (strpos($line, "data-producto") !== false && strpos($line, "id") !== false)) {
        echo "Line " . str_pad($i+1, 4) . ": " . substr($line, 0, 150) . "\n";
        $found++;
    }
    if ($found >= 15) break;
}
?>