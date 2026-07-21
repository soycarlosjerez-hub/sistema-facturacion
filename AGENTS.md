# Subagentes del Proyecto

Este proyecto utiliza subagentes especializados para desarrollo eficiente.

## Agentes Disponibles

| Agente | Descripción | Trigger |
|--------|-------------|---------|
| `@backend` | Especialista Laravel/PHP — controladores, servicios, modelos, APIs, middleware, políticas | "backend", "controlador", "servicio", "modelo", "API", "middleware", "policy", "permiso", "rol" |
| `@frontend` | Especialista Blade/CSS/JS — vistas, UI premium, DataTables, Bootstrap, Vue, responsive | "vista", "blade", "frontend", "UI", "CSS", "DataTables", "premium", "responsive", "dark mode" |
| `database` | Especialista BD — migrations, relaciones, seeders, optimización, índices | "migration", "schema", "tabla", "relación", "seeder", "factory", "índice" |
| `testing` | Especialista testing/debug — PHPUnit, debugging, logging, profiling | "test", "debug", "error", "bug", "log", "problema", "no funciona" |
| `orchestrator` | Coordina subagentes para tareas complejas — flujos completos de desarrollo | "crear módulo completo", "CRUD completo", "implementar desde cero", "orquestar" |
| `contable-rd` | Especialista contable RD — DGII, NCF, ITBIS, retenciones, cuentas, normativas fiscales dominicanas | "contable", "DGII", "NCF", "ITBIS", "comprobante", "tributario", "impuesto", "contabilidad", "retención" |

## Ubicación

Los agentes están en `.opencode/agent/`:
```
.opencode/agent/
├── backend.md
├── contable-rd.md
├── database.md
├── frontend.md
├── orchestrator.md
└── testing.md
```

## Cómo Usar

### Auto-detección
Los agentes se invocan automáticamente según la descripción. Por ejemplo, si pides "crear un controlador", se invocará `@backend`.

### Invocación Manual
Usa `@nombre-agente` para invocar uno específico:
```
@backend crea un servicio para productos
@frontend aplica UI premium a la vista de gastos
@database agrega una columna a la tabla de facturas
@testing depura el error en ventas
@contable-rd explica el proceso de retencion ITBIS
```

### Flujo Completo
Para crear un módulo completo, simplemente describe lo que necesitas:
```
Crear módulo de categorías con CRUD completo
```
El `orchestrator` coordinará database → backend → frontend → testing automáticamente.
