# Changelog — Erpipos ERP

## [Unreleased]

### Added (2026-08-30)

#### Fase 4 — Seguridad, Monitoreo y Producción

**Middleware y Seguridad**
- `SecurityHeadersMiddleware.php` — Headers adicionales (HSTS, Permissions-Policy, etc.)
- `QueryProfileMiddleware.php` — Profiling de queries lentas (>100ms)
- `CorsMiddleware.php` — Control de orígenes permitidos
- Registrados en `bootstrap/app.php` y aplicados globalmente

**Monitoreo y Observabilidad**
- `app/Providers/TelescopeServiceProvider.php` — ServiceProvider Telescope
- `config/telescope.php` — Configuración de Telescope
- Telescope accessible en `/telescope` (solo admin/owner/root)
- Query profiling en canal `query_profile` (solo debug)
- Sentry config en `config/sentry.php` (placeholder DSN)

**Backups**
- `app/Services/BackupService.php` — Servicio completo (168 líneas)
  - createBackup(), restoreBackup(), deleteBackup()
  - cleanOldBackups(), verifyBackup(), getStats()
  - BackupService registrado y usable desde controladores
- `app/Services/S3BackupService.php` — Backups a AWS S3
  - uploadBackup(), downloadBackup(), deleteBackup()
  - listBackups(), generateDatabaseDump()
  - fullBackupToS3(), rotateBackups()
- `app/Jobs/GenerateBackup.php` — Job de backup async

**Jobs**
- `app/Jobs/SendEmailNotification.php` — Notificación por email
- `app/Jobs/SendInvoiceEmail.php` — Envío de factura por email
- `app/Jobs/SendTwoFactorSetupEmail.php` — Notificación 2FA por email
- `app/Jobs/ProcessEcfDocumentos.php` — Procesamiento e-CF DGII

**Deploy**
- `scripts/deploy.sh` — Script de deploy completo con rollback automático
- `scripts/deploy-production.sh` — Deploy prod con Slack notifications

**Documentación**
- `openapi.yml` — OpenAPI 3.0.3 spec completo
- `docs/API.md` — 300+ líneas de documentación de API
- `README.md` actualizado con Fase 4 completa

**Docker Production**
- `docker-compose.production.yml` — 279 líneas
  - Nginx con SSL/HTTPS
  - MySQL 8.0 optimizado
  - Redis con persistencia
  - 2 Queue Workers
  - Scheduler
  - Certbot integration
  - PHPMyAdmin
  - Healthchecks
  - Resource limits

**Configuración**
- `.env.example` actualizado (199 líneas) con:
  - Todas las variables de config
  - Secciones organizadas: App, DB, Redis, Queue, Email, AWS, DGII, 2FA, Rate Limit, Backups, Telescope, Sentry, etc.
  - Comentarios en español explicando cada variable

**Optimización BD**
- `database/migrations/2026_08_30_000001_add_indexes_for_performance.php`
  - 18 tablas optimizadas con 50+ índices
  - Nombres descriptivos con prefijo de tabla
  - Cada índice documentado con comentario

**Testing E2E**
- `tests/e2e/01-auth.spec.ts` — Login, registro, auth
- `tests/e2e/02-profile.spec.ts` — Perfil, seguridad
- `tests/e2e/03-sales.spec.ts` — Ventas (7 tests)
- `tests/e2e/04-inventory.spec.ts` — Inventario (7 tests)
- `tests/e2e/05-2fa.spec.ts` — 2FA (5 tests)
- `playwright.config.ts` — Config multi-browser (Chrome, Firefox, Safari)

**PHPUnit**
- `tests/Feature/TwoFactorAuthTest.php` — 5 tests de 2FA
- Total: 17 tests Unit pasando (39 assertions)

### Changed
- `bootstrap/app.php` — Registrados nuevos middlewares y aplicados globalmente
- `.env.example` — 117 a 199 líneas, documentación completa
- `README.md` — Agregadas secciones: Docker, Seguridad, Testing, Monitoreo, Roadmap

### Fixed
- `.env` — Corregido DB_PASSWORD sin comillas
- `app/Models/Cliente.php` — Fix in `nombreCompleto()`
- `app/Support/Sidebar.php` — Fix en sección de seguridad

---

## [2.0.0] — 2026-08-30

### Fase 4 — Completada
- ✅ Sentry monitoring integration
- ✅ Query profiling middleware
- ✅ S3 backup service
- ✅ Docker production setup
- ✅ Deploy automation
- ✅ Security headers
- ✅ CORS middleware
- ✅ E2E tests (22 tests)

### Fase 3 — En progreso
- 🟡 Contabilidad completa
- 🟡 CRM
- 🟡 Mobile App
- 🟡 Multi-currency

---

## [1.5.0] — 2026-08-30

### Fase 2 — Completada
- ✅ PHPUnit tests (17 passing)
- ✅ GitHub Actions CI/CD
- ✅ Playwright E2E tests
- ✅ Rate limiting middleware
- ✅ Security headers
- ✅ FormRequest validators

---

## [1.0.0] — 2026-08-30

### Fase 1 — Completada
- ✅ README.md (387 líneas)
- ✅ .env.example (117 líneas)
- ✅ OwnerController → 7 controladores especializados
- ✅ SaleService → 6 servicios especializados
- ✅ 2FA/Google Authenticator
- ✅ CSP middleware
- ✅ Policies (43 existentes)
- ✅ Migración in_array() → hasAnyRole() (26 instancias)

---

## [0.1.0] — Estado inicial
- MVP con funcionalidades básicas
- Facturación electrónica
- Multi-tenancy
- 11 verticales de negocio
