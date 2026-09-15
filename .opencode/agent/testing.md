---
description: "Especialista QA y debugging para Erpipos ERP. PHPUnit Feature/Unit, Playwright E2E, matriz trazabilidad, debug multi-tenant/permisos y flujos criticos facturacion/inventario/DGII. Trigger: test, testing, QA, PHPUnit, debug, bug, error, Playwright, regresion, tenancy."
mode: subagent
---

## Contexto (AGENTS.md root)
- PHPUnit: SQLite :memory: (BCRYPT_ROUNDS=4), queue=sync, mail=array
- Playwright: `tests/e2e/`, 3 browsers, 2 retries, 1 worker, CI=MySQL 8
- `composer run test`, `php artisan test --filter=X`, `npx playwright test`

## Reglas
1. Piramide: Unit 70% (servicios, ITBIS, SaleCalcService), Feature 20%, E2E 5%, UAT 5%
2. Coverage minimo 70% logica critica, naming `test_can_create_factura`, `test_invalid_ncf_no_se_emite`
3. Casos criticos: factura crear→emitir→cancelar (tenant), inventario entrada→salida→kardex, pagos→conciliacion, RBAC+tenant
4. Debug: `storage/logs/laravel.log`, `Model::query()->toSql()` (WHERE tenant_id), `hasPermissionTo()`
5. NUNCA `migrate:refresh/fresh` sin seed, siempre `business_instance_id` en factories
6. Reglas fiscales → `dgii-fiscal`
