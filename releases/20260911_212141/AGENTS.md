# Erpipos ERP — Agent Instructions

Laravel 12 / PHP 8.2+ multi-tenant SaaS for Dominican Republic e-CF (DGII) electronic invoicing.

## ⚠️ CRITICAL — READ FIRST

- **NEVER use `php artisan migrate:refresh`** — ever. It destroys ALL data including seeded tenants, users, roles, permissions, and configuration.
- **NEVER use `php artisan migrate:fresh`** without `--seed` unless the user explicitly asks for a full reset.
- If migrations fail and you need to reset: use `php artisan migrate:fresh --seed` only on explicit user request.
- `tenant_id` never accepted from user input — always assign from `auth()->user()->business_instance_id`.
- Use `hasAnyRole()` for permission checks, NEVER `in_array()`. Roles/permissions are per-instance (Spatie laravel-permission).

## Project Stats

| Layer | Location | Count |
|-------|----------|-------|
| Models | `app/Models/` | 171 (many use `TenantScope`, `Auditable`) |
| Migrations | `database/migrations/` | 377 |
| Seeders | `database/seeders/` | 48 (+ 65 in `Full/`) |
| Controllers | `app/Http/Controllers/` | 131 (+ Api/) |
| Services | `app/Services/` (Ecf/, Ai/) | 78 |
| Blade views | `resources/views/` | 532 Blade files in ~79 dirs |
| Routes | `routes/web.php` | ~1,200 |
| Routes | `routes/api.php` | ~143 |

## Setup

```bash
composer install && npm install && cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build        # production assets
npm run dev          # dev server (vite + queue + serve via composer dev)
```

Dev: `composer run dev` (concurrently: `php artisan serve`, `php artisan queue:listen --tries=1`, `npm run dev`).

## Multi-Tenancy (Critical)

- Custom `TenantScope` trait at `app/Traits/TenantScope.php` — auto-scopes queries by `tenant_id` on 80+ models.
- Check for `use TenantScope` on a model before writing queries; models without the trait are **not** scoped.
- When creating models that appear in views: **always set `tenant_id`** (assign from `auth()->user()->business_instance_id`).
- `MULTITENANCY_ENABLED=true` in `.env` (default). Set `HOSTNAME_TENANCY=false` unless using subdomain routing.

## Testing

```bash
composer run test       # clears config then runs php artisan test
php artisan test         # PHPUnit (SQLite :memory: — BCRYPT_ROUNDS=4 in phpunit.xml)
php artisan test --filter=Venta   # single test
npx playwright test      # E2E (boots artisan serve on :8000, 3 browsers)
npx playwright test tests/e2e/01-auth.spec.ts  # single file
npx playwright test --ui             # interactive mode
```

- PHPUnit: SQLite in-memory, isolated per run. Queue = `sync`, mail = `array`.
- Playwright E2E: 5 test files in `tests/e2e/`. CI: 2 retries, 1 worker.
- CI uses MySQL 8.0 service container (not SQLite).

## DGII / e-CF (Domain-Specific)

- `.env` var `DGII_AMBIENTE` (sandbox | qa | prod). Locally simulated by default (`DGII_SIMULAR=true`).
- Production: real `.crt`/`.key` certs, HTTPS, real API key.
- ECF services: `app/Services/Ecf/`. State machine trait: `app/Concerns/HasEcfStateMachine`.
- Certificates/XML: `storage/dgii/`.
- Currency: `RD$` default. ITBIS default: 18%.

## Frontend

- Blade templates + Alpine.js 3 (not Inertia).
- SCSS: `resources/scss/dashboard.scss` + `resources/scss/app.scss` → compiled by Vite.
- JS: `resources/js/dashboard.js` (Chart.js 4) + `resources/js/app.js`.
- Premium UI: glassmorphism, dark mode, DataTables, animated gradients.

## Docker

```bash
docker compose up -d                          # dev (php-fpm, nginx, mysql, redis, phpmyadmin, queue)
docker compose -f docker-compose.yml \
  -f docker-compose.production.yml up -d      # production with Let's Encrypt SSL
```

- Deploy: `sudo bash scripts/deploy.sh staging` / `sudo bash scripts/deploy-production.sh`.

## Constraints

- PSR-12 coding style. Lint: `composer run pint` (Laravel Pint).
- FormRequest validators for all write operations.
- Routes via route files only (`routes/web.php`, `routes/api.php`). Flash messages in Spanish.
- Permissions format: `{modulo}.{accion}` (view, create, edit, delete).
