#!/bin/bash
# ============================================================================
# Erpipos ERP — Deploy Script (Staging / Production)
# ============================================================================
# Uso:
#   sudo bash scripts/deploy.sh [staging|production]
# ============================================================================

set -e

ENVIRONMENT=${1:-production}
PROJECT_PATH="/var/www/html/sistema-facturacion"
DEPLOY_DIR="${PROJECT_PATH}/releases/$(date +%Y%m%d_%H%M%S)"
CURRENT_DIR="${PROJECT_PATH}/current"
BRANCH=$(git rev-parse --abbrev-ref HEAD)

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${BLUE}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

log_info "=== Erpipos ERP Deploy — $ENVIRONMENT ==="
log_info "Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
log_info "Usuario: $(whoami)"
log_info "Branch: $BRANCH"

if [ "$(id -u)" -ne 0 ]; then
    log_error "Este script debe ejecutarse con sudo o como root"
    exit 1
fi

if [ ! -d "${PROJECT_PATH}/.git" ]; then
    log_error "Directorio $PROJECT_PATH no es un repositorio Git"
    exit 1
fi

log_info "Creando directorio de release: $DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR"

log_info "Copiando archivos del proyecto..."
rsync -a --exclude='.git' --exclude='storage/' --exclude='node_modules/' \
    --exclude='.env' --exclude='vendor/' \
    "$PROJECT_PATH/" "$DEPLOY_DIR/"

log_info "Instalando dependencias PHP..."
cd "$DEPLOY_DIR"
composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

log_info "Instalando dependencias Node..."
npm ci --only=production 2>/dev/null || npm install --production 2>/dev/null || true
npm run build 2>/dev/null || true

if [ "$ENVIRONMENT" = "production" ]; then
    cp .env.production .env 2>/dev/null || cp .env.example .env
else
    cp .env.staging .env 2>/dev/null || cp .env.example .env
fi

php artisan key:generate --force 2>/dev/null || true

log_info "Ejecutando migraciones..."
php artisan migrate --force 2>/dev/null || log_warn "Migraciones fallaron (puede ser normal)"

log_info "Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize

log_info "Limpiando cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

log_info "Link de storage..."
php artisan storage:link 2>/dev/null || true

log_info "Ajustando permisos..."
chmod -R 755 "$DEPLOY_DIR/storage"
chmod -R 755 "$DEPLOY_DIR/bootstrap/cache"
chown -R www-data:www-data "$DEPLOY_DIR/storage"
chown -R www-data:www-data "$DEPLOY_DIR/bootstrap/cache"

log_info "Creando symlink a current..."
if [ -L "$CURRENT_DIR" ]; then
    rm "$CURRENT_DIR"
fi
ln -s "$DEPLOY_DIR" "$CURRENT_DIR"

log_info "Limpiando releases antiguos..."
cd "${PROJECT_PATH}/releases"
ls -td */ 2>/dev/null | tail -n +6 | xargs rm -rf 2>/dev/null || true

rollback() {
    log_error "Deploy falló. Ejecutando rollback..."
    if [ -L "$CURRENT_DIR" ]; then
        OLD_RELEASE=$(readlink "$CURRENT_DIR")
        log_info "Rolling back a: $OLD_RELEASE"
        rm "$CURRENT_DIR"
        ln -s "$OLD_RELEASE" "$CURRENT_DIR"
        log_success "Rollback completado."
    else
        log_error "No hay release anterior para rollback"
    fi
    exit 1
}

trap rollback ERR

log_info "Verificando deploy..."
cd "$CURRENT_DIR"

if php artisan --version > /dev/null 2>&1; then
    log_success "Deploy exitoso para $ENVIRONMENT!"
    log_info "Release: $DEPLOY_DIR"
else
    log_error "La app no responde. Verifica los logs."
    exit 1
fi

log_info "=== Deploy completado ==="
echo -e "${GREEN}✓ Deploy exitoso para $ENVIRONMENT${NC}"
echo -e "  Release: $DEPLOY_DIR"
echo -e "  Branch:  $BRANCH"
echo -e "  Hora:    $(date '+%Y-%m-%d %H:%M:%S')"
