<?php
// Direct database check using PDO
$host = 'localhost';
$db   = 'sistema_facturacion';  // or whatever the database name is
$user = 'root';  // WAMP default
$pass = '';      // WAMP default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // List all printers
    $stmt = $pdo->query("SELECT * FROM impresoras");
    $impresoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== IMPRESORAS EN BASE DE DATOS ===\n\n";
    foreach ($impresoras as $imp) {
        echo "ID: " . $imp['id'] . "\n";
        echo "  Nombre: " . $imp['nombre'] . "\n";
        echo "  Tipo Conexión: " . $imp['tipo_conexion'] . "\n";
        echo "  Driver: " . $imp['driver'] . "\n";
        echo "  Papel Tamano: " . $imp['papel_tamano'] . "\n";
        echo "  Caracteres por línea: " . $imp['caracteres_por_linea'] . "\n";
        echo "  Ruta Compartida: " . $imp['ruta_compartida'] . "\n";
        echo "  Activo: " . ($imp['activo'] ? 'Sí' : 'No') . "\n";
        echo "--- \n";
    }
    
    if (empty($impresoras)) {
        echo "No hay impresoras configuradas en la base de datos.\n";
    }
    
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage() . "\n";
}