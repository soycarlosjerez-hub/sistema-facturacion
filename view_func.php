<?php
$file = "resources/views/ventas/create.blade.php";
$content = file_get_contents($file);
$lines = explode("\n", $content);

// Find line 3913 and show 30 lines around it
$start = 3900;
$end = $start + 50;
for ($i = $start; $i < min($end, count($lines)); $i++) {
    echo ($i+1) . ": " . substr($lines[$i], 0, 150) . "\n";
}
?>