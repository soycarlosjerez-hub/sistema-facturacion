#!/bin/bash
# Log rotation cron job for Erpipos ERP
# Run daily via cron: 0 2 * * * /var/www/html/sistema-facturacion/scripts/rotate_logs.sh

APP_PATH="/var/www/html/sistema-facturacion"
LOG_PATH="$APP_PATH/storage/logs"
BACKUP_PATH="/tmp/logs_backup_$(date +%Y%m%d)"
MAX_LOG_SIZE=104857600  # 100MB per log file

mkdir -p "$BACKUP_PATH"

# Rotate and compress large log files
find "$LOG_PATH" -name "*.log" -type f -size +${MAX_LOG_SIZE}c -exec sh -c '
    for f; do
        cp "$f" "$BACKUP_PATH/$(basename "$f").$(date +%s)"
        gzip "$BACKUP_PATH/$(basename "$f").$(date +%s)"
        truncate -s 0 "$f"
    done
' sh {} +

# Compress old uncompressed backups
find "$BACKUP_PATH" -name "*.log.*" ! -name "*.gz" -exec gzip {} \; 2>/dev/null

# Cleanup backups older than 7 days
find "$BACKUP_PATH" -type f -mtime +7 -delete 2>/dev/null

# Archive backups older than 24 hours
if [ -d "$BACKUP_PATH" ] && [ "$(find "$BACKUP_PATH" -type f | wc -l)" -gt 0 ]; then
    tar -czf "/tmp/logs_weekly_$(date +%Y%m%d).tar.gz" -C "$BACKUP_PATH" . 2>/dev/null
    rm -rf "$BACKUP_PATH"
fi

# Clean up old weekly archives (keep 4 weeks)
find /tmp -name "logs_weekly_*.tar.gz" -mtime +28 -delete 2>/dev/null

# Run Laravel log rotate command
cd "$APP_PATH" && php artisan log:rotate 2>/dev/null

echo "$(date): Log rotation completed" >> /tmp/log_rotation.log
