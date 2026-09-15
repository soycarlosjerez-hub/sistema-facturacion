# Erpipos ERP — Agent Instructions

Laravel 12 / PHP 8.2+ multi-tenant SaaS para Republica Dominicana e-CF (DGII) facturacion electronica.

## ⚠️ CRITICO — OBLIGATORIO

- **NUNCA `php artisan migrate:refresh`** — destruye TODOS los datos (tenants, usuarios, roles, permisos).
- **NUNCA `php artisan migrate:fresh`** sin `--seed` a menos que el usuario pida reset completo.
- `tenant_id` nunca se acepta del usuario — siempre `auth()->user()->business_instance_id`.
- Usa `hasAnyRole()` para permisos, NUNCA `in_array()`. Roles/permisos son por instancia (Spatie laravel-permission).

## Proyecto

| Capa | Ubicacion | Cantidad |
|------|-----------|----------|
| Modelos | `app/Models/` | 171 (many `TenantScope`, `Auditable`) |
| Migraciones | `database/migrations/` | 377 |
| Seeders | `database/seeders/` | 48 (+ 65 `Full/`) |
| Controladores | `app/Http/Controllers/` | 131 (+ Api/) |
| Servicios | `app/Services/` | 78 |
| Vistas Blade | `resources/views/` | 532 en ~79 dirs |
| Rutas web | `routes/web.php` | ~1,200 |
| Rutas API | `routes/api.php` | ~143 |

## Setup

```bash
composer install && npm install && cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build        # prod
npm run dev          # dev (vite + queue + serve)
```

Dev: `composer run dev` (php artisan serve, queue:listen, npm run dev).

## Multi-Tenancy

- `TenantScope` en `app/Traits/TenantScope.php` — auto-filtra por `tenant_id` en 80+ modelos.
- Si un modelo NO tiene `use TenantScope` no esta scoped.
- Al crear modelos en vistas: siempre `tenant_id` desde `auth()->user()->business_instance_id`.
- `MULTITENANCY_ENABLED=true` en `.env` (default). `HOSTNAME_TENANCY=false` si no usas subdominios.

## Testing

```bash
composer run test       # php artisan test
php artisan test --filter=Venta   # test unico
npx playwright test      # E2E (serve :8000, 3 browsers)
npx playwright test tests/e2e/01-auth.spec.ts  # unico archivo
```

PHPUnit: SQLite :memory:, queue=sync, mail=array. Playwright: 5 archivos en `tests/e2e/`.

## DGII / e-CF

- `DGII_AMBIENTE` (sandbox|qa|prod), `DGII_SIMULAR=true` local.
- e-CF: `app/Services/Ecf/` + `SaleEcfService`, state machine: `app/Concerns/HasEcfStateMachine`.
- Certs/XML: `storage/dgii/`. Moneda: `RD$`, ITBIS: 18%.

## Frontend

- Blade + Alpine.js 3 + Bootstrap 5.3. SCSS: `resources/scss/dashboard.scss` → Vite.
- JS: `resources/js/dashboard.js` (Chart.js 4) + `app.js`.
- UI Premium: glassmorphism, dark mode, DataTables, gradientes animados.

## Docker

```bash
docker compose up -d                          # dev
docker compose -f docker-compose.yml -f docker-compose.production.yml up -d  # prod SSL
```

Deploy: `sudo bash scripts/deploy.sh staging` / `sudo bash scripts/deploy-production.sh`.

## Restricciones

- PSR-12. Lint: `composer run pint` (Laravel Pint).
- FormRequest para toda write operation.
- Rutas solo en archivos (web.php, api.php). Flash en espanol.
- Permisos: `{modulo}.{accion}` (view, create, edit, delete).
- Reglas de negocio: consultar `business-analyst` / `dgii-fiscal`.
