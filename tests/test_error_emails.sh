#!/bin/bash
###############################################################################
# test_error_emails.sh
# Prueba completa del envío de correos de error con SMTP de BD (global)
# Uso: ./test_error_emails.sh [--recipient email] [--force] [--dry-run]
###############################################################################

set -euo pipefail

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

# Variables
RECIPIENT=""
FORCE=false
DRY_RUN=false
BASE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
PROJECT_DIR="$(dirname "$BASE_DIR")"

# Parsear argumentos
while [[ $# -gt 0 ]]; do
    case $1 in
        --recipient)
            RECIPIENT="$2"
            shift 2
            ;;
        --force)
            FORCE=true
            shift
            ;;
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        *)
            echo -e "${RED}Error: Argumento desconocido '$1'${NC}"
            echo "Uso: $0 [--recipient email] [--force] [--dry-run]"
            exit 1
            ;;
    esac
done

# Destinatario por defecto
if [ -z "$RECIPIENT" ]; then
    RECIPIENT="jcjerez@gmail.com"
fi

echo -e "${CYAN}╔══════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║   Prueba de Correos de Error con SMTP de BD     ║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  Destinatario: ${GREEN}${RECIPIENT}${NC}"
echo -e "  Modo: $(if [ "$DRY_RUN" = true ]; then echo -e "${YELLOW}DRY-RUN${NC}"; else echo -e "${GREEN}REAL${NC}"; fi)"
echo ""

# Función de log
log_step() {
    echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}▶ $1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
}

log_ok() {
    echo -e "  ${GREEN}✓ $1${NC}"
}

log_fail() {
    echo -e "  ${RED}✗ $1${NC}"
}

log_info() {
    echo -e "  ${CYAN}ℹ $1${NC}"
}

log_warn() {
    echo -e "  ${YELLOW}⚠ $1${NC}"
}

# Resultado final
TOTAL_TESTS=0
PASS_TESTS=0
FAIL_TESTS=0

check_result() {
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    if [ "$1" -eq 0 ]; then
        PASS_TESTS=$((PASS_TESTS + 1))
    else
        FAIL_TESTS=$((FAIL_TESTS + 1))
    fi
}

###############################################################################
# FASE 1: Verificación de entorno
###############################################################################
log_step "FASE 1: Verificación de Entorno"

# Verificar que estamos en el directorio correcto
if [ ! -f "$PROJECT_DIR/artisan" ]; then
    log_fail "No se encontró artisan en $PROJECT_DIR"
    echo "  Asegúrate de ejecutar el script desde la carpeta tests/"
    exit 1
fi
log_ok "Directorio del proyecto detectado"

# Verificar PHP
if ! command -v php &> /dev/null; then
    log_fail "PHP no está instalado"
    exit 1
fi
PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2)
log_ok "PHP version: $PHP_VERSION"

# Verificar Composer
if ! command -v composer &> /dev/null; then
    log_fail "Composer no está instalado"
    exit 1
fi
log_ok "Composer disponible"

# Verificar que vendor existe
if [ ! -d "$PROJECT_DIR/vendor" ]; then
    log_fail "Directorio vendor no encontrado. Ejecuta: composer install"
    exit 1
fi
log_ok "Vendor directory existe"

# Verificar que .env existe
if [ ! -f "$PROJECT_DIR/.env" ]; then
    log_fail ".env no encontrado"
    exit 1
fi
log_ok ".env existe"

# Verificar MAIL_MAILER en .env
MAIL_MAILER_ENV=$(grep "^MAIL_MAILER=" "$PROJECT_DIR/.env" | cut -d'=' -f2 | tr -d '[:space:]')
log_info "MAIL_MAILER en .env: $MAIL_MAILER_ENV"
if [ "$MAIL_MAILER_ENV" = "log" ]; then
    log_warn "MAIL_MAILER=log en .env — Esto es esperado, los correos deben usar SMTP de BD"
else
    log_info "MAIL_MAILER=$MAIL_MAILER_ENV en .env"
fi

###############################################################################
# FASE 2: Verificación de configuración SMTP en BD
###############################################################################
log_step "FASE 2: Verificación de Configuración SMTP en Base de Datos"

# Verificar que existen settings SMTP globales (tenant_id=NULL)
SMTP_GLOBAL_COUNT=$(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('system_settings')->whereNull('tenant_id')->where('key', 'like', 'mail_%')->count();" 2>/dev/null | tail -1)

if [ -z "$SMTP_GLOBAL_COUNT" ] || [ "$SMTP_GLOBAL_COUNT" = "0" ]; then
    log_fail "No hay settings SMTP globales (tenant_id=NULL) en la BD"
    
    if [ "$FORCE" = true ]; then
        log_info "Modo --force: Intentando insertar settings SMTP por defecto..."
        
        cd "$PROJECT_DIR"
        php artisan tinker --execute="
\$settings = [
    ['key' => 'mail_mailer', 'value' => 'smtp'],
    ['key' => 'mail_host', 'value' => 'mail.armada.do'],
    ['key' => 'mail_port', 'value' => '465'],
    ['key' => 'mail_username', 'value' => 'no-reply@armada.do'],
    ['key' => 'mail_encryption', 'value' => 'ssl'],
    ['key' => 'mail_from_address', 'value' => 'no-reply@armada.do'],
    ['key' => 'mail_from_name', 'value' => 'Sistema de Facturación'],
    ['key' => 'error_alert_email', 'value' => '${RECIPIENT}'],
];
foreach (\$settings as \$s) {
    DB::table('system_settings')->updateOrInsert(
        ['key' => \$s['key'], 'tenant_id' => null],
        ['value' => \$s['value']]
    );
}
echo 'Settings insertados.';
" 2>/dev/null
        
        if [ $? -eq 0 ]; then
            log_ok "Settings SMTP insertados exitosamente"
        else
            log_fail "Error al insertar settings SMTP"
            check_result 1
            echo -e "\n${RED}═══════════════════════════════════════════════════${NC}"
            echo -e "${RED}RESULTADO FINAL: ${FAIL_TESTS} fallos, ${PASS_TESTS} éxitos${NC}"
            echo -e "${RED}═══════════════════════════════════════════════════${NC}"
            exit 1
        fi
    else
        log_warn "Ejecuta con --force para insertar settings por defecto"
        check_result 1
    fi
else
    log_ok "Existen $SMTP_GLOBAL_COUNT settings SMTP globales en la BD"
fi

# Verificar error_alert_email
ERROR_ALERT_EMAIL=$(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('system_settings')->whereNull('tenant_id')->where('key','error_alert_email')->value('value');" 2>/dev/null | tail -1)

if [ -z "$ERROR_ALERT_EMAIL" ]; then
    log_fail "error_alert_email no configurado en la BD"
    log_info "Configúralo en Owner > Configuración SMTP"
    check_result 1
else
    log_ok "error_alert_email configurado: $ERROR_ALERT_EMAIL"
fi

# Verificar mail_host
MAIL_HOST=$(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('system_settings')->whereNull('tenant_id')->where('key','mail_host')->value('value');" 2>/dev/null | tail -1)

if [ -z "$MAIL_HOST" ]; then
    log_fail "mail_host no configurado en la BD"
    check_result 1
else
    log_ok "mail_host configurado: $MAIL_HOST"
fi

# Verificar que NO hay settings SMTP de tenant (limpieza)
SMTP_TENANT_COUNT=$(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('system_settings')->whereNotNull('tenant_id')->where('key', 'like', 'mail_%')->count();" 2>/dev/null | tail -1)

if [ -n "$SMTP_TENANT_COUNT" ] && [ "$SMTP_TENANT_COUNT" != "0" ]; then
    log_warn "Existen $SMTP_TENANT_COUNT settings SMTP de tenant — Se recomienda ejecutar: php artisan smtp:cleanup"
else
    log_ok "No hay settings SMTP de tenant (limpio)"
fi

###############################################################################
# FASE 3: Limpieza de jobs antiguos
###############################################################################
log_step "FASE 3: Limpieza de Jobs Antiguos"

JOBS_PENDING=$(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('jobs')->count();" 2>/dev/null | tail -1)
log_info "Jobs pendientes en cola: $JOBS_PENDING"

FAILED_JOBS=$(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('failed_jobs')->count();" 2>/dev/null | tail -1)
log_info "Jobs fallidos: $FAILED_JOBS"

if [ "$DRY_RUN" = false ]; then
    # Limpiar jobs antiguos con mailer=log
    cd "$PROJECT_DIR"
    php artisan tinker --execute="
\$count = DB::table('jobs')->where('payload', 'LIKE', '%\"mailer\":\"log\"%')->delete();
echo \"Jobs eliminados con mailer=log: \$count\";
" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        log_ok "Jobs antiguos limpiados"
    else
        log_warn "No se pudieron limpiar jobs antiguos"
    fi
else
    log_info "[DRY-RUN] Saltando limpieza de jobs"
fi

###############################################################################
# FASE 4: Verificación de ErrorMailer service
###############################################################################
log_step "FASE 4: Verificación de ErrorMailer Service"

if [ -f "$PROJECT_DIR/app/Services/ErrorMailer.php" ]; then
    log_ok "ErrorMailer.php existe"
    
    # Verificar que tiene los métodos necesarios
    HAS_APPLY=$(grep -c "applyGlobalSmtp" "$PROJECT_DIR/app/Services/ErrorMailer.php" || true)
    HAS_GET_EMAIL=$(grep -c "getAlertEmail" "$PROJECT_DIR/app/Services/ErrorMailer.php" || true)
    
    if [ "$HAS_APPLY" -gt 0 ] && [ "$HAS_GET_EMAIL" -gt 0 ]; then
        log_ok "ErrorMailer tiene los métodos applyGlobalSmtp() y getAlertEmail()"
    else
        log_fail "ErrorMailer está incompleto"
        check_result 1
    fi
else
    log_fail "ErrorMailer.php no encontrado"
    check_result 1
fi

# Verificar que bootstrap/app.php usa ErrorMailer
BOOTSTRAP_USES_ERROR_MAIKER=$(grep -c "ErrorMailer" "$PROJECT_DIR/bootstrap/app.php" || true)
if [ "$BOOTSTRAP_USES_ERROR_MAIKER" -gt 0 ]; then
    log_ok "bootstrap/app.php usa ErrorMailer"
else
    log_fail "bootstrap/app.php NO usa ErrorMailer"
    check_result 1
fi

# Verificar que LogErrorToDatabase usa ErrorMailer
LISTENER_USES_ERROR_MAIKER=$(grep -c "ErrorMailer" "$PROJECT_DIR/app/Listeners/LogErrorToDatabase.php" || true)
if [ "$LISTENER_USES_ERROR_MAIKER" -gt 0 ]; then
    log_ok "LogErrorToDatabase.php usa ErrorMailer"
else
    log_fail "LogErrorToDatabase.php NO usa ErrorMailer"
    check_result 1
fi

###############################################################################
# FASE 5: Prueba de envío directo SMTP
###############################################################################
log_step "FASE 5: Prueba de Envío Directo SMTP"

if [ "$DRY_RUN" = true ]; then
    log_info "[DRY-RUN] Saltando prueba de envío directo"
else
    cd "$PROJECT_DIR"
    
    # Enviar correo de prueba directo
    RESULT=$(php artisan tinker --execute="
try {
    \App\Services\ErrorMailer::applyGlobalSmtp();
    \Mail::raw('Prueba de envío SMTP desde test_error_emails.sh', function(\$m) {
        \$m->to('${RECIPIENT}')
          ->subject('[PRUEBA] Test Error Emails - SMTP Directo');
    });
    echo 'SENT';
} catch (\Exception \$e) {
    echo 'FAIL:' . \$e->getMessage();
}
" 2>/dev/null | tail -1)
    
    if [ "$RESULT" = "SENT" ]; then
        log_ok "Correo de prueba SMTP enviado exitosamente a $RECIPIENT"
        log_info "Revisa tu bandeja de entrada (puede tardar unos segundos)"
    elif [[ "$RESULT" == FAIL:* ]]; then
        ERROR_MSG="${RESULT#FAIL:}"
        log_fail "Error al enviar correo SMTP: $ERROR_MSG"
        check_result 1
    else
        log_fail "Resultado inesperado al enviar correo: $RESULT"
        check_result 1
    fi
fi

###############################################################################
# FASE 6: Verificación de tabla instance_error_logs
###############################################################################
log_step "FASE 6: Verificación de Tabla instance_error_logs"

TABLE_EXISTS=$(cd "$PROJECT_DIR" && php artisan tinker --execute="echo Schema::hasTable('instance_error_logs') ? 'yes' : 'no';" 2>/dev/null | tail -1)

if [ "$TABLE_EXISTS" = "yes" ]; then
    log_ok "Tabla instance_error_logs existe"
    
    ERROR_LOG_COUNT=$(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('instance_error_logs')->count();" 2>/dev/null | tail -1)
    log_info "Registros existentes: $ERROR_LOG_COUNT"
else
    log_fail "Tabla instance_error_logs no existe"
    check_result 1
fi

###############################################################################
# FASE 7: Prueba de creación de error log
###############################################################################
log_step "FASE 7: Prueba de Creación de Error Log"

if [ "$DRY_RUN" = true ]; then
    log_info "[DRY-RUN] Saltando prueba de creación de error log"
else
    cd "$PROJECT_DIR"
    
    # Crear un error log de prueba
    CREATE_RESULT=$(php artisan tinker --execute="
try {
    \$log = \App\Models\InstanceErrorLog::create([
        'tenant_id' => null,
        'level' => 'warning',
        'title' => 'Test Error Email - Prueba Automatizada',
        'message' => 'Este es un error de prueba creado por test_error_emails.sh. Puede ignorarlo.',
        'context' => ['test' => true, 'script' => 'test_error_emails.sh'],
        'source' => 'test_script',
        'file' => 'test_error_emails.sh',
        'line' => 1,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'TestScript/1.0',
    ]);
    echo 'CREATED:' . \$log->id;
} catch (\Exception \$e) {
    echo 'FAIL:' . \$e->getMessage();
}
" 2>/dev/null | tail -1)
    
    if [[ "$CREATE_RESULT" == CREATED:* ]]; then
        LOG_ID="${CREATE_RESULT#CREATED:}"
        log_ok "Error log de prueba creado (ID: $LOG_ID)"
    else
        log_fail "Error al crear error log de prueba: $CREATE_RESULT"
        check_result 1
    fi
fi

###############################################################################
# FASE 8: Verificación de cola de trabajos
###############################################################################
log_step "FASE 8: Verificación de Cola de Trabajos"

JOBS_AFTER=$(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('jobs')->count();" 2>/dev/null | tail -1)
log_info "Jobs después de pruebas: $JOBS_AFTER"

# Verificar queue workers
QUEUE_WORKERS_RUNNING=$(ps aux | grep "queue:work" | grep -v grep | wc -l || echo "0")
if [ "$QUEUE_WORKERS_RUNNING" -gt 0 ]; then
    log_ok "Queue workers activos detectados"
else
    log_warn "No hay queue workers activos — Los correos en cola no se procesarán hasta iniciar uno"
    log_info "Inicia con: php artisan queue:work --queue=errors --sleep=3 --tries=3"
fi

###############################################################################
# FASE 9: Verificación de vista SMTP
###############################################################################
log_step "FASE 9: Verificación de Vista SMTP"

VIEW_FILE="$PROJECT_DIR/resources/views/owner/smtp-settings.blade.php"
if [ -f "$VIEW_FILE" ]; then
    log_ok "Vista smtp-settings.blade.php existe"
    
    HAS_ERROR_ALERT_FIELD=$(grep -c "error_alert_email" "$VIEW_FILE" || true)
    if [ "$HAS_ERROR_ALERT_FIELD" -gt 0 ]; then
        log_ok "Campo error_alert_email presente en la vista"
    else
        log_fail "Campo error_alert_email NO encontrado en la vista"
        check_result 1
    fi
else
    log_fail "Vista smtp-settings.blade.php no encontrada"
    check_result 1
fi

# Verificar Controller
CONTROLLER_FILE="$PROJECT_DIR/app/Http/Controllers/OwnerController.php"
if [ -f "$CONTROLLER_FILE" ]; then
    CONTROLLER_HAS_EMAIL=$(grep -c "error_alert_email" "$CONTROLLER_FILE" || true)
    if [ "$CONTROLLER_HAS_EMAIL" -gt 0 ]; then
        log_ok "OwnerController maneja error_alert_email"
    else
        log_fail "OwnerController NO maneja error_alert_email"
        check_result 1
    fi
else
    log_fail "OwnerController no encontrado"
    check_result 1
fi

###############################################################################
# FASE 10: Resumen de configuración
###############################################################################
log_step "FASE 10: Resumen de Configuración Actual"

echo ""
echo -e "  ${CYAN}Configuración SMTP Global:${NC}"
echo -e "    Host:        ${MAIL_HOST:-NO CONFIGURADO}"
echo -e "    Port:        $(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('system_settings')->whereNull('tenant_id')->where('key','mail_port')->value('value');" 2>/dev/null | tail -1)"
echo -e "    Username:    $(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('system_settings')->whereNull('tenant_id')->where('key','mail_username')->value('value');" 2>/dev/null | tail -1)"
echo -e "    Encryption:  $(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('system_settings')->whereNull('tenant_id')->where('key','mail_encryption')->value('value');" 2>/dev/null | tail -1)"
echo -e "    From:        $(cd "$PROJECT_DIR" && php artisan tinker --execute="echo DB::table('system_settings')->whereNull('tenant_id')->where('key','mail_from_address')->value('value');" 2>/dev/null | tail -1)"
echo ""
echo -e "  ${CYAN}Alertas de Error:${NC}"
echo -e "    Destinatario: ${ERROR_ALERT_EMAIL:-NO CONFIGURADO}"
echo ""

###############################################################################
# RESULTADO FINAL
###############################################################################
echo ""
echo -e "${CYAN}╔══════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║              RESULTADO FINAL                     ║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  Total de pruebas: ${TOTAL_TESTS}"
echo -e "  ${GREEN}Pasadas: ${PASS_TESTS}${NC}"
echo -e "  ${RED}Fallidas: ${FAIL_TESTS}${NC}"
echo ""

if [ "$FAIL_TESTS" -eq 0 ]; then
    echo -e "  ${GREEN}══════════════════════════════════════════════════${NC}"
    echo -e "  ${GREEN}  ✅ TODAS LAS PRUEBAS PASARON EXITOSAMENTE      ${NC}"
    echo -e "  ${GREEN}══════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "  ${GREEN}Resumen:${NC}"
    echo -e "    • ErrorMailer service configurado correctamente"
    echo -e "    • bootstrap/app.php usa SMTP global de BD"
    echo -e "    • LogErrorToDatabase usa SMTP global de BD"
    echo -e "    • Campo error_alert_email en vista y controller"
    if [ "$DRY_RUN" = false ]; then
        echo -e "    • Correo de prueba enviado a $RECIPIENT"
        echo -e "    • Error log de prueba creado"
    fi
    echo ""
    echo -e "  ${YELLOW}Nota:${NC} Para que los correos en cola se procesen:"
    echo -e "    php artisan queue:work --queue=errors --sleep=3 --tries=3"
    echo ""
    exit 0
else
    echo -e "  ${RED}══════════════════════════════════════════════════${NC}"
    echo -e "  ${RED}  ❌ HAY PRUEBAS QUE FALLARON                     ${NC}"
    echo -e "  ${RED}══════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "  ${YELLOW}Revisa los pasos marcados con ✗ arriba.${NC}"
    echo ""
    exit 1
fi
