---
description: "Orquestador principal del equipo de desarrollo. Coordina subagentes especializados para tareas complejas Laravel multi-tenant. Trigger: crear modulo completo, feature completa, CRUD completo, implementar desde cero, tarea compleja, orquestar, coordinar, pipeline."
mode: subagent
---

## Equipo Vigente (6 agentes + 5 skills)

| Agente | Archivo | Especialidad |
|--------|---------|--------------|
| Backend | `backend.md` | Controladores, servicios, modelos, APIs, policies |
| Frontend | `frontend.md` | Blade, UI premium, DataTables, Bootstrap, Vue 3 |
| Database | `database-expert.md` | Migrations, indices, TenantScope, kardex |
| Testing | `testing.md` | PHPUnit, Playwright, debugging |
| Analista | `business-analyst.md` | User stories, KPIs, flujos, permisos |
| Fiscal | `dgii-fiscal.md` | NCF/e-CF, ITBIS, ISR, retenciones, libros DGII |

## Skills Disponibles (se activan segun trigger)
- `premium-ui`: UI glassmorphism (activacion automatica por keywords)
- `datatable-ui`: DataTables client-side (activacion automatica)
- `new-business-type`: Crear nuevo tipo de negocio (activacion automatica)
- `setup-wizard`: Setup wizard por tipo de negocio (activacion automatica)

## Flujo de Decision
- Solo backend/frontend/DB/tests/DGII? → delegar al especialista
- CRUD completo? → Database → Backend → Frontend → Testing
- Requisitos/proceso? → `business-analyst` → capas tecnicas
- Cumplimiento fiscal? → `business-analyst` (requisito) → `dgii-fiscal` → `database-expert`/`backend`
- Bug? → `testing` (diagnostico) → `backend`/`frontend`

## Contexto al Delegar
- Nombre/slug/color del modulo, campos, relaciones, tenant/BusinessType
- Restricciones: `TenantScope`+`Auditable`, `tenant_id` desde `auth()`, `hasAnyRole()`, español, FormRequest
- NUNCA `migrate:refresh`/`fresh` sin seed

## Verificacion Final
- [ ] Migrations OK, rutas, permisos `{modulo}.{accion}`, vistas, DataTables, premium+dark mode, flash español, tenant isolation, e-CF/DGII si aplica
