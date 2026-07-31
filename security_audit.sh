#!/bin/bash
# ============================================================
# AUDITORIA DE SEGURIDAD - SISTEMA FACTURACION RD
# Ejecutar como root o con sudo
# ============================================================

echo "=============================================="
echo "AUDITORIA DE SEGURIDAD"
echo "Fecha: $(date)"
echo "Servidor: $(hostname)"
echo "=============================================="
echo ""

# Color codes
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
NC='\033[0m'

# ============================================================
# 1. USUARIOS SOSPECHOSOS
# ============================================================
echo -e "${YELLOW}[1/7] Buscando usuarios sospechosos...${NC}"
echo "----------------------------------------------"
MYSQL_PASS=""
read -sp "Password de MySQL root (dejar vacio si no tiene): " MYSQL_PASS
echo ""

RESULT=$(sudo mysql -u root -p"$MYSQL_PASS" -sse "USE sistema_facturacion; SELECT id, name, email, created_at FROM users WHERE email LIKE '%sharklasers%' OR email LIKE '%ov%' OR name LIKE '%ov%';" 2>/dev/null)
if [ $? -eq 0 ] && [ ! -z "$RESULT" ]; then
    echo -e "${RED}USUARIOS SOSPECHOSOS ENCONTRADOS:${NC}"
    echo "$RESULT" | column -t -s$'\t'
else
    echo -e "${GREEN}No se encontraron usuarios sospechosos.${NC}"
fi
echo ""

# ============================================================
# 2. ACCESOS DESDE IP SOSPECHOSA
# ============================================================
echo -e "${YELLOW}[2/7] Buscando accesos desde IP 179.52.9.32...${NC}"
echo "----------------------------------------------"

for LOGFILE in /var/log/nginx/access.log /var/log/apache2/access.log /var/log/httpd/access_log; do
    if [ -f "$LOGFILE" ]; then
        echo "Archivo: $LOGFILE"
        COUNT=$(grep -c "179.52.9.32" "$LOGFILE" 2>/dev/null || echo "0")
        echo "Total accesos: $COUNT"
        if [ "$COUNT" -gt 0 ]; then
            echo "Ultimos 10 accesos:"
            grep "179.52.9.32" "$LOGFILE" 2>/dev/null | tail -10
        fi
        break
    fi
done
echo ""

# ============================================================
# 3. SESIONES ACTIVAS SOSPECHOSAS
# ============================================================
echo -e "${YELLOW}[3/7] Buscando sesiones sospechosas en BD...${NC}"
echo "----------------------------------------------"
SESSIONS=$(sudo mysql -u root -p"$MYSQL_PASS" -sse "USE sistema_facturacion; SELECT id, user_id, ip_address, last_activity, host FROM sessions WHERE ip_address = '179.52.9.32' OR user_id IS NOT NULL ORDER BY last_activity DESC LIMIT 50;" 2>/dev/null)
if [ ! -z "$SESSIONS" ]; then
    echo "$SESSIONS" | column -t -s$'\t'
else
    echo -e "${GREEN}No se encontraron sesiones sospechosas.${NC}"
fi
echo ""

# ============================================================
# 4. ARCHIVOS PHP MODIFICADOS RECIENTEMENTE
# ============================================================
echo -e "${YELLOW}[4/7] Buscando archivos PHP modificados en ultimas 48h...${NC}"
echo "----------------------------------------------"
RECENT_FILES=$(find /var/www/html/sistema-facturacion -name "*.php" -mtime -2 -type f 2>/dev/null | grep -v vendor | grep -v storage/logs | grep -v node_modules)
if [ ! -z "$RECENT_FILES" ]; then
    echo "Archivos modificados recientemente:"
    echo "$RECENT_FILES" | nl
    echo ""
    echo "Archivos en directorios criticos:"
    find /var/www/html/sistema-facturacion/public -name "*.php" -mtime -2 -type f 2>/dev/null
    find /var/www/html/sistema-facturacion/bootstrap -name "*.php" -mtime -2 -type f 2>/dev/null
    find /var/www/html/sistema-facturacion/config -name "*.php" -mtime -2 -type f 2>/dev/null
else
    echo -e "${GREEN}No se encontraron archivos PHP modificados recientemente.${NC}"
fi
echo ""

# ============================================================
# 5. BUSQUEDA DE CODIGO MALICIOSO
# ============================================================
echo -e "${YELLOW}[5/7] Buscando codigo potencialmente malicioso...${NC}"
echo "----------------------------------------------"

MALICIOUS_PATTERNS=("eval\(|base64_decode\(|assert\(|shell_exec\(|system\(|passthru\(|exec\(|popen\(|proc_open\(|gzuncompress\|create_function\|array_map.*create_function|preg_replace.*\/e|\\$_REQUEST|\\$_GLOBALS|\\$_COOKIE.*=.*\\$_REQUEST|wso2|c99|r57|webshell|backdoor|cmd=|command=|eval\(base64"

FOUND_MALICIOUS=false
for DIR in /var/www/html/sistema-facturacion/app /var/www/html/sistema-facturacion/public /var/www/html/sistema-facturacion/resources; do
    if [ -d "$DIR" ]; then
        for PATTERN in ${MALICIOUS_PATTERNS[@]}; do
            MATCHES=$(grep -rl "$PATTERN" "$DIR" --include="*.php" 2>/dev/null | grep -v vendor | grep -v storage/logs)
            if [ ! -z "$MATCHES" ]; then
                echo -e "${RED}ALERTA: Patron sospechoso encontrado:$NC"
                echo "  Patron: $PATTERN"
                echo "  Archivos:"
                echo "$MATCHES" | sed 's/^/    /'
                FOUND_MALICIOUS=true
            fi
        done
    fi
done

if [ "$FOUND_MALICIOUS" = false ]; then
    echo -e "${GREEN}No se encontro codigo malicioso obvio.${NC}"
fi
echo ""

# ============================================================
# 6. WEB SHELLS COMUNES
# ============================================================
echo -e "${YELLOW}[6/7] Buscando web shells comunes...${NC}"
echo "----------------------------------------------"

WEB_SHELL_NAMES=("shell.php" "cmd.php" "c99.php" "r57.php" "wso.php" "alfa.php" "b374k.php" "webadmin.php" "admin.php.bak" "tmp.php" "upload.php" "img.php" "sys.php" "info.php" "phpinfo.php" "test.php" "debug.php" "console.php" "terminal.php" "backdoor.php" "webshell.php")

FOUND_SHELLS=false
for FILENAME in ${WEB_SHELL_NAMES[@]}; do
    FOUND=$(find /var/www/html/sistema-facturacion/public -name "$FILENAME" -type f 2>/dev/null)
    if [ ! -z "$FOUND" ]; then
        echo -e "${RED}WEB SHELL POTENCIAL ENCONTRADO:$NC $FOUND"
        FOUND_SHELLS=true
    fi
done

if [ "$FOUND_SHELLS" = false ]; then
    echo -e "${GREEN}No se encontraron web shells comunes.${NC}"
fi
echo ""

# ============================================================
# 7. CRONTAB Y PROCESOS SOSPECHOSOS
# ============================================================
echo -e "${YELLOW}[7/7] Revisando cronjobs y procesos sospechosos...${NC}"
echo "----------------------------------------------"

echo "Crontab de root:"
crontab -l 2>/dev/null || echo "  (vacio)"
echo ""

echo "Crontabs de otros usuarios:"
cut -d: -f1 /etc/passwd | while read USER; do
    CRON=$(crontab -l -u "$USER" 2>/dev/null)
    if [ ! -z "$CRON" ]; then
        echo "  Usuario: $USER"
        echo "$CRON" | sed 's/^/    /'
    fi
done
echo ""

echo "Archivos en /etc/cron.d/:"
ls -la /etc/cron.d/ 2>/dev/null | grep -v "^total"
echo ""

echo "Procesos PHP sospechosos (fuera de apache/nginx):"
ps aux | grep php | grep -v "apache\|nginx\|grep\|cron" | head -10
echo ""

echo "Conexiones de red activas:"
netstat -tlnp 2>/dev/null | grep LISTEN | head -20
echo ""

# ============================================================
# RESUMEN FINAL
# ============================================================
echo "=============================================="
echo -e "${GREEN}AUDITORIA COMPLETADA${NC}"
echo "=============================================="
echo ""
echo "ARCHIVOS DE LOG RELEVANTES:"
echo "  /var/log/nginx/access.log (o apache2/access.log)"
echo "  /var/log/nginx/error.log (o apache2/error.log)"
echo "  /var/log/auth.log (accesos SSH)"
echo ""
echo "COMANDOS ADICIONALES RECOMENDADOS:"
echo "  sudo tail -100 /var/log/auth.log | grep FAILED"
echo "  sudo last | head -20"
echo "  sudo find /var/www/html/sistema-facturacion/storage/framework/views -name '*.php' -mtime -1"
echo "  sudo ls -la /var/www/html/sistema-facturacion/.env"
echo ""
echo "SI CONFIRMAS COMPROMISO:"
echo "  1. Cambiar TODAS las contraseñas inmediatamente"
echo "  2. Revisar claves SSH autorizadas: cat ~/.ssh/authorized_keys"
echo "  3. Verificar usuarios del sistema: cat /etc/passwd"
echo "  4. Contactar a un especialista en ciberseguridad"
echo "  5. Restaurar desde backup limpio si es necesario"
echo ""
