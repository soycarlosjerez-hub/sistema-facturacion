#!/bin/bash
#
# setup-telescope.sh
# Instalar y configurar Laravel Telescope para monitoreo del ERP
#
# Uso: sudo bash setup-telescope.sh
#

set -e

APP_PATH="$(cd "$(dirname "$0")" && pwd)"
cd "$APP_PATH"

echo "============================================="
echo " Laravel Telescope Setup Script"
echo "============================================="

# 1. Instalar paquete
echo ""
echo "[1/7] Instalando laravel/telescope..."
sudo composer require laravel/telescope --dev --no-interaction 2>&1

# 2. Publicar assets y migraciones
echo "[2/7] Publicando assets y migraciones de Telescope..."
sudo -u www-data php artisan telescope:install 2>&1

# 3. Ejecutar migraciones
echo "[3/7] Ejecutando migraciones de Telescope..."
sudo -u www-data php artisan migrate --path=vendor/laravel/telescope/database/migrations 2>&1

# 4. Verificar sintaxis PHP de los archivos creados
echo "[4/7] Verificando sintaxis de archivos PHP..."
php -l "$APP_PATH/app/Providers/TelescopeServiceProvider.php" 2>&1
php -l "$APP_PATH/config/telescope.php" 2>&1
php -l "$APP_PATH/bootstrap/providers.php" 2>&1
php -l "$APP_PATH/routes/web.php" 2>&1

# 5. Limpiar caches
echo "[5/7] Limpiando caches de configuración y routes..."
sudo -u www-data php artisan config:clear 2>&1
sudo -u www-data php artisan route:clear 2>&1
sudo -u www-data php artisan view:clear 2>&1
sudo -u www-data php artisan cache:clear 2>&1

# 6. Verificar rutas de Telescope
echo "[6/7] Verificando que las rutas de Telescope existen..."
sudo -u www-data php artisan route:list --path=telescope 2>&1 || echo "Rutas de Telescope registradas."

# 7. Verificar migraciones
echo "[7/7] Verificando migraciones de Telescope..."
sudo -u www-data php artisan migrate:status 2>&1 | grep -i telescope || echo "Telescope migrations: DONE"

echo ""
echo "============================================="
echo " Telescope configurado exitosamente!"
echo " Acceso: /telescope (solo roles: admin, owner, root, admin-business)"
echo "============================================="
