# Erpipos ERP — Sistema de Facturación Multi-Tenant

![Laravel 12](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel)
![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![SaaS](https://img.shields.io/badge/Architecture-SaaS-brightgreen?style=for-the-badge)
![Multi-Tenant](https://img.shields.io/badge/Tenancy-Multi--Tenant-blue?style=for-the-badge)
![e-CF DGII](https://img.shields.io/badge/DGII-e--CF%20RD-%230066CC?style=for-the-badge)

**Erpipos ERP** es un sistema empresarial completo para Republica Dominicana con facturación electrónica DGII (e-CF), multi-tenancy SaaS, y 11 verticales de negocio especializados. Diseñado para PYMES que necesitan un ERP profesional sin la complejidad de SAP o Oracle.

---

## 📊 Estadísticas del Proyecto

| Métrica | Valor |
|---------|-------|
| Modelos Eloquent | 158 |
| Tablas en BD | 200+ |
| Controladores | 130+ |
| Servicios | 66+ |
| Vistas Blade | 436 |
| Rutas Web | 500+ |
| Rutas API | 100+ |
| Permisos Spatie | 80+ |
| Tipos de Negocio | 11 |
| Tests Feature | 15+ |

---

## ✨ Características Principales

### 🧾 Facturación y Cumplimiento Legal (DGII RD)

| Funcionalidad | Estado | Descripción |
|--------------|:------:|-------------|
| e-CF | ✅ | Comprobante Fiscal Electrónico — generación XML, firma digital, envío, consulta, anulación |
| NCF | ✅ | Gestión de Números de Control — tipos (B01, E01, E02, F01, X01, etc.), secuencias, vencimiento |
| Certificados Digitales | ✅ | Carga y gestión de certificados SSL/DGII |
| Libro de Ventas | ✅ | Reporte DGII con export CSV/PDF |
| Libro de Compras | ✅ | Reporte DGII con export CSV/PDF |
| Libro de Retenciones | ✅ | Retenciones ITBIS e ISR |
| Formulario 14-14 | ✅ | Declaración de pagos a no residentes |
| Reportes 606/607 | ✅ | Declaraciones trimestrales obligatorias |

### 🖥️ Puntos de Venta (POS)

- POS en tiempo real con búsqueda por código de barras
- Múltiples métodos de pago: Efectivo, Tarjeta, Transferencia, Mixto, Fiado, Cuenta Abierta
- Split Bill — División de cuenta por persona
- Propinas y cargo de servicio configurables
- Cajas y turnos — Apertura, cierre, resumen de caja

### 📦 Inventario

- Multi-almacén con kardex completo
- Listas de precio — Por cliente o general, con impacto calculado
- Import/Export de productos (Excel/CSV)
- Códigos de barras — Generación y escaneo
- Stock mínimo y alertas automáticas

### 🏢 Módulos por Vertical de Negocio (11 tipos)

| Vertical | Módulos Incluidos |
|----------|-------------------|
| Retail | POS, inventario, listas de precio, delivery |
| Mayorista | Volúmenes, listas de precio por cliente |
| Restaurante | Mesas, órdenes, KDS (cocina), reservaciones, waitlist |
| Lavadero | Vehículos, citas/turnos, tipos de servicio |
| Tattoo Studio | Artistas, diseños, citas, encargos con progreso |
| Alquileres | Viviendas, inquilinos, contratos, pagos mensuales |
| Tecnología | Equipos por IMEI, órdenes de reparación, técnicos |
| Climatización | Tipos de equipo, instalaciones, contratos de mantenimiento |
| Delivery | Empresas, drivers, tracking en tiempo real |
| Mecánica | Vehículos, órdenes, servicios |
| Arte/Galería | Obras, artistas, colecciones, exhibiciones |

### 🌐 Multi-Tenancy SaaS

- Aislamiento total por tenant_id en todas las tablas transaccionales
- Global Scope `TenantScope` en 120+ modelos (auto-filtra por instancia)
- Roles por instancia — no globales, cada cliente tiene sus propios roles
- Módulos activables/desactivables por tipo de negocio
- Setup Wizard adaptativo según tipo de negocio
- Planes SaaS con limitaciones verificadas
- API Keys por instancia para integración externa
- Owner Panel completo para gestionar todas las instancias

### 🔒 Seguridad y Auditoría

- Roles y permisos con Spatie Laravel-Permissions (80+ permisos)
- Policies para todos los recursos
- Logs de auditoría polimórficos (todas las acciones registradas)
- Backups manuales y automáticos
- Notificaciones en tiempo real (polling cada 10 segundos)
- Two-Factor Authentication (2FA) — Opcional, con QR y recovery codes
- CSP Headers — Content Security Policy configurada

### 💻 Tecnología

- AI Assistant integrado — Chat con IA
- ISO 9001 — SGC completo (documentos, no conformidades, auditorías, riesgos)
- API REST — 100+ endpoints con autenticación API Key y Sanctum
- Multi-sucursal
- Multi-idioma (ES/EN)

### 🎨 UI/UX Premium

- Glassmorphism — Con backdrop-filter, gradientes animados
- Dark Mode completo — Implementado en TODO el sistema
- Responsive Design — Mobile-first con breakpoints
- Accesibilidad WCAG 2.1 AA
- DataTables interactivas con búsqueda y paginación
- Charts con Chart.js con dark mode adaptativo
- Búsqueda global con atajo `Ctrl+K`
- Toast notifications

---

## 🖥️ Requerimientos del Sistema

| Componente | Versión Mínima | Recomendada |
|------------|---------------|-------------|
| PHP | 8.2 | 8.3+ |
| MySQL/MariaDB | 8.0 / 10.6 | 8.0+ / 10.11+ |
| Composer | 2.x | 2.x |
| Node.js | 18 | 20+ |
| NPM | 9 | 10+ |
| SSL | — | Obligatorio para DGII |
| Memoria PHP | 512MB | 1GB+ |

---

## 📥 Instalación Paso a Paso

### 1. Clonar el proyecto

```bash
git clone https://github.com/tu-usuario/erpipos.git
cd erpipos
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Instalar dependencias frontend

```bash
npm install
```

### 4. Configurar variables de entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configurar base de datos

Edita `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erpipos
DB_USERNAME=root
DB_PASSWORD=your-strong-password
```

### 6. Ejecutar migraciones

```bash
php artisan migrate
```

### 7. Crear symlinks de storage

```bash
php artisan storage:link
```

### 8. Instalar seeders (datos iniciales)

```bash
php artisan db:seed
```

### 9. Construir assets frontend

```bash
npm run build
```

### 10. Iniciar queue worker

```bash
php artisan queue:work
```

### 11. (Producción) Configurar Supervisor

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/erpipos/artisan queue:work
autostart=true
autorestart=true
stopwaitsecs=60
numprocs=4
```

---

## 📁 Estructura del Proyecto

```
app/
├── Concerns/              # Traits compartidos (HasEcfStateMachine)
├── Events/                # 10 eventos (SaleCreated, ShiftOpened, etc.)
├── Http/
│   ├── Controllers/       # 130+ controladores (base + Api/)
│   ├── Middleware/        # 14 middlewares (CSP, Permission, 2FA, etc.)
│   └── Requests/          # 45+ form requests de validación
├── Jobs/                  # Jobs en cola
├── Listeners/              # 11 listeners de eventos
├── Models/                # 158 modelos Eloquent
├── Policies/              # 40+ policies de autorización
├── Services/              # 66+ servicios (incluye Ecf/, Ai/)
└── Providers/             # Service providers

database/
├── factories/             # Factory para testing
├── migrations/            # 338 migraciones (11 años de evolución)
└── seeders/               # 103 seeders (incluye Full/ con 72 archivos)

resources/
├── css/                   # SCSS (Bootstrap 5.3, custom premium UI)
├── js/                    # Vite bundle (Alpine.js 3, Chart.js)
└── views/                 # 436 vistas Blade en 79 directorios

routes/
├── api.php                # 100+ endpoints API REST
├── auth.php               # Breeze auth (60 lineas)
├── console.php            # Artisan commands (15 lineas)
└── web.php                # 500+ rutas web
```

---

## ⚙️ Configuración

### Variables de Entorno Importantes

```env
# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erpipos
DB_USERNAME=root
DB_PASSWORD=your-password

# Mail (SMTP) — Necesario para notificaciones y alertas DGII
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=no-reply@your-domain.com
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls

# DGII — Facturacion Electronica
DGII_AMBIENTE=sandbox          # sandbox | qa | prod
DGII_API_URL_PROD=https://api.dgii.gov.do
DGII_CERT_STORAGE_PATH=storage/dgii/certs
DGII_CERT_PASSWORD=your-cert-password
DGII_API_KEY_PROD=your-api-key

# Storage
FILESYSTEM_DISK=local          # local | s3

# Two-Factor Authentication
# Se habilita por usuario desde /two-factor

# Backups
BACKUP_PATH=storage/backups    # Directorio para backups (chmod 755)
```

### Configuración DGII (Producción)

Para producción, necesitas:

1. **Solicitar certificados** en la DGII:
   - Certificado digital SSL (.crt y .key)
   - API Key para e-CF

2. **Configurar en .env:**

```env
DGII_AMBIENTE=prod
DGII_API_URL_PROD=https://api.dgii.gov.do
DGII_API_KEY_PROD=tu-api-key
DGII_CERT_CLIENT_CERT_PROD=/path/to/cert.crt
DGII_CERT_CLIENT_KEY_PROD=/path/to/key.key
DGII_CERT_CLIENT_KEY_PASS_PROD=contraseña-certificado
```

---

## 🚀 Uso Rapido

### 1. Crear un tenant de prueba

```bash
# 1. Acceder como owner (crear en .env o seeders)
# 2. Ir a /owner
# 3. Crear nueva instancia
# 4. Configurar con Setup Wizard
```

### 2. Usar la API

```bash
# Obtener token de API
curl -X POST "https://dominio.com/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@demo.com","password":"password"}'

# Hacer petición con el token
curl -X GET "https://dominio.com/api/products?page=1" \
  -H "Authorization: Bearer TU_TOKEN" \
  -H "Accept: application/json"
```

### 3. Acceder al sistema

- **URL:** `https://your-domain.com`
- **Login:** credentials generados por seeder
- **Owner Panel:** `https://your-domain.com/owner` (rol owner)

---

## 📖 Contribuir

1. Fork el proyecto
2. Crear branch de feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push al branch (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

### Estilos de código

- PSR-12 como estándar
- Tipado estricto en métodos
- DocBlocks para funciones públicas
- Nombres descriptivos para variables y métodos

---

## 📄 License

MIT License — Ver archivo LICENSE para detalles.

---

## 📞 Soporte y Contacto

| Recurso | Enlace |
|---------|--------|
| Documentación API | `/docs/api` (en sistema) |
| Issues | https://github.com/tu-usuario/erpipos/issues |
| Email | soporte@erpipos.com |
| Web | https://erpipos.com |

---


---

## 🐳 Producción con Docker

### Desarrollo
```bash
docker compose up -d
```

### Producción (con SSL)
```bash
docker compose -f docker-compose.yml -f docker-compose.production.yml up -d

# Obtener SSL (Let's Encrypt)
# Reemplaza DOMAIN_NAME en docker/nginx/nginx.conf y ejecuta:
sudo docker run --rm -p 80:80   -v /etc/letsencrypt:/etc/letsencrypt   -v /var/www/certbot:/var/www/certbot   certbot/certbot certonly --webroot --webroot-path=/var/www/certbot   -d your-domain.com --email admin@your-domain.com --agree-tos --non-interactive --no-eff-email
```

### Deploy automatizado
```bash
# Staging
sudo bash scripts/deploy.sh staging

# Producción (con Slack notification si configuras SLACK_WEBHOOK)
sudo SLACK_WEBHOOK="https://hooks.slack.com/..." bash scripts/deploy-production.sh
```

### Variables de entorno clave
| Variable | Descripción | Valor default |
|----------|-------------|---------------|
| `APP_ENV` | Entorno (local/staging/production) | local |
| `DB_CONNECTION` | Tipo de BD | mysql |
| `REDIS_HOST` | Host de Redis | 127.0.0.1 |
| `QUEUE_CONNECTION` | Conexión de queues | database |
| `MAIL_MAILER` | Driver de email | smtp |
| `TELESCOPE_ENABLED` | Monitor Telescope | false |
| `SENTRY_LARAVEL_DSN` | Sentry DSN | (vacío) |

---

## 🔒 Seguridad

### Headers de seguridad implementados
- **CSP** — Content Security Policy
- **HSTS** — HTTP Strict Transport Security
- **X-Frame-Options** — DENY
- **X-Content-Type-Options** — nosniff
- **X-XSS-Protection** — 1; mode=block
- **Referrer-Policy** — strict-origin-when-cross-origin
- **Permissions-Policy** — camera=(), microphone=()
- **CSRF Protection** — Laravel CSRF tokens

### Autenticación
- **2FA** — Google Authenticator / Authy
- **Password hashing** — bcrypt con 12 rounds
- **JWT** — Tokens API con expiración
- **Session encryption** — Sesiones cifradas

### Protección de API
- **Rate limiting** — 60 requests/min
- **CORS** — Orígenes permitidos configurables
- **Authentication middleware** — api-auth
- **Tenant isolation** — Multi-tenancy forzada

---

## 🧪 Testing

### PHPUnit (Unit & Feature)
```bash
php artisan test                    # Todos los tests
php artisan test --testsuite=Unit   # Solo Unit
php artisan test --testsuite=Feature # Solo Feature
php artisan test --filter=Venta     # Por nombre
```

### Playwright (E2E)
```bash
npx playwright test                 # Todos los E2E tests
npx playwright test tests/e2e/01-auth.spec.ts  # Solo auth
npx playwright test --ui             # Modo interactivo
npx playwright test --debug          # Debug mode
```

### Cobertura actual
| Suite | Tests | Estado |
|-------|:------:|:------:|
| Unit | 17 | ✅ |
| Feature | 15+ | ✅ |
| 2FA Auth | 5 | ✅ |
| E2E Auth | 3 | ✅ |
| E2E Profile | 2 | ✅ |
| E2E Sales | 7 | ✅ |
| E2E Inventory | 7 | ✅ |
| E2E 2FA | 5 | ✅ |

---

## 📊 Monitoreo y Observabilidad

### Laravel Telescope
- URL: `/telescope` (solo admin/owner/root)
- Request monitoring
- Query profiling
- Exception tracking
- Scheduled jobs log
- Cache operations
- Notifications

### Query Profiling
- Middleware para log de queries lentas (>100ms)
- Headers de respuesta: `X-Total-Queries`, `X-Query-Time`
- Canal de log: `query_profile`

### Sentry (opcional)
- Configurar `SENTRY_LARAVEL_DSN` en .env
- Captura automática de errores
- Performance monitoring (traces)
- Release tracking

---

## 🗺️ Roadmap Actualizado

| Fase | Estado | Características |
|------|:------:|-----------------|
| **Fase 1** ✅ | ✅ Completado | Documentación, refactorización, 2FA, CSP, FormRequest, migración in_array→hasAnyRole |
| **Fase 2** ✅ | ✅ Completado | PHPUnit, Playwright E2E, CI/CD GitHub Actions, Rate Limiting, Security Headers |
| **Fase 3** ⏳ | En progreso | Contabilidad completa, CRM, App Mobile (Flutter), multi-currency, Docker production |
| **Fase 4** ✅ | ✅ Completado | Sentry monitoring, query profiling, S3 backups, deploy automation, security hardening |

---

## 📝 Changelog

### v2.0.0 — Fase 4 (2026-08-30)
- ✅ Sentry monitoring integration
- ✅ Query profiling middleware
- ✅ S3 backup service (AWS)
- ✅ Docker production setup con SSL
- ✅ Deploy automation scripts
- ✅ CORS middleware
- ✅ Security headers adicionales
- ✅ 26 instancias de `in_array()` migradas a `hasAnyRole()`
- ✅ Facade SaleService corregido
- ✅ 2FA accesible desde perfil de usuario
- ✅ E2E tests: Auth, Profile, Sales, Inventory, 2FA
- ✅ API documentation (OpenAPI + Markdown)
- ✅ Database indexes optimization (18 tablas)
- ✅ Jobs: SendEmail, SendInvoice, GenerateBackup, ProcessEcf, 2FA email
- ✅ BackupService completo con 168 líneas
- ✅ .env.example actualizado (199 líneas)
- ✅ Telescope integration
- ✅ 63 archivos modificados, 105 nuevos

### v1.5.0 — Fase 2 (2026-08-30)
- ✅ PHPUnit tests (17 passing)
- ✅ GitHub Actions CI/CD
- ✅ Playwright E2E tests
- ✅ Rate limiting middleware
- ✅ Security headers (CSP, HSTS, X-Frame-Options, etc.)
- ✅ FormRequest validators (+8 nuevos)
- ✅ Tests para 2FA (5 tests)

### v1.0.0 — Fase 1 (2026-08-30)
- ✅ README.md completo (387 líneas)
- ✅ .env.example documentado (117 líneas)
- ✅ OwnerController dividido en 7 controladores especializados
- ✅ SaleService dividido en 6 servicios especializados
- ✅ 2FA/Google Authenticator integrado
- ✅ CSP middleware implementado
- ✅ Policies de autorización (43 existentes)

## 🗺️ Roadmap (Fase 2-4)

| Fase | Estado | Características |
|------|:------:|-----------------|
| **Fase 1** ✅ | ✅ Completado | Documentación, refactorización, 2FA, CSP, FormRequest |
| **Fase 2** ⏳ | En progreso | Tests E2E (Playwright), CI/CD, coverage 50%, workflow/approbaciones |
| **Fase 3** ⏳ | Planificado | Contabilidad completa, CRM, App Mobile (Flutter), multi-currency |
| **Fase 4** ✅ | ✅ Completado | Sentry, profiling, S3 backups, deploy, security hardening, Docker production |
