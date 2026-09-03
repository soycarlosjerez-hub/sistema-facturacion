#!/bin/bash
# Script para dar permisos de escritura a los archivos del proyecto
# Ejecutar con: sudo bash dar-permisos.sh

PROJECT_PATH="/var/www/html/sistema-facturacion"

echo "Dando permisos de escritura al proyecto..."

# Cambiar propietario del directorio
sudo chown -R $(whoami):www-data "$PROJECT_PATH"

# Dar permisos de lectura/escritura al propietario y lectura al grupo
sudo find "$PROJECT_PATH" -type f -exec chmod 664 {} \;
sudo find "$PROJECT_PATH" -type d -exec chmod 775 {} \;

# Asegurar que los directorios que necesitan escritura tengan permisos correctos
sudo chmod -R 775 "$PROJECT_PATH/storage"
sudo chmod -R 775 "$PROJECT_PATH/bootstrap/cache"

echo "Permisos aplicados correctamente."
echo ""
echo "Verificando archivo específico:"
ls -la "$PROJECT_PATH/resources/views/owner/instances/config.blade.php"
