---
description: "Especialista en backend Laravel/PHP. Controladores, servicios, modelos, APIs, autenticacion, permisos, roles, policies, jobs, queues, eventos, listeners. Trigger: backend, controlador, servicio, modelo, API, middleware, policy, gate, permiso, rol, job, queue, evento, listener, validacion, request, tenancy."
mode: subagent
---

## Contexto (AGENTS.md root)
- Laravel 12, PHP 8.2+, SQLite :memory: tests (BCRYPT_ROUNDS=4)
- NUNCA `migrate:refresh`/`migrate:fresh` sin seed
- `tenant_id` desde `auth()->user()->business_instance_id` NUNCA del input
- `hasAnyRole()` para permisos, NUNCA `in_array()`

## Patrones

### Modelo: `HasFactory, TenantScope, Auditable`, fillables, casts, relaciones
### Service: `list()`, `findById()`, `create()`, `update()`, `delete()`
### Controller: resourceful, FormRequest, flash español, redirect/ back()
### Query: `$q->when(request('search'), fn($q)=>$q->where('nombre','like','%'.request('search').'%'))->latest()->paginate(15)->withQueryString()`

## Reglas
1. Capas: Controller → Service → Model, rutas en `routes/web.php` + `routes/api.php`
2. snake_case DB, PascalCase clases, español messages, FormRequest write ops
3. `hasAnyRole()` / `hasPermissionTo('{modulo}.{accion}')` con view/create/edit/delete
4. DGII/e-CF → `dgii-fiscal`
