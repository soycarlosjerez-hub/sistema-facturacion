# Subagentes del Proyecto

Este proyecto utiliza subagentes especializados para desarrollo eficiente.

## Agentes Disponibles

| Agente | Descripción | Trigger |
|--------|-------------|---------|
| `@backend` | Especialista Laravel/PHP — controladores, servicios, modelos, APIs, middleware, políticas | "backend", "controlador", "servicio", "modelo", "API", "middleware", "policy", "permiso", "rol" |
| `@frontend` | Especialista Blade/CSS/JS — vistas, UI premium, DataTables, Bootstrap, Vue, responsive | "vista", "blade", "frontend", "UI", "CSS", "DataTables", "premium", "responsive", "dark mode" |
| `database-expert` | Especialista Senior BD — MySQL, diseño modelos ERP, normalización, índices, optimización SQL, migraciones, rendimiento | "migration", "schema", "tabla", "relación", "seeder", "factory", "índice", "optimización", "query", "performance", "BD", "MySQL", "modelo de datos", "normalización" |
| `testing` | Especialista testing/debug — PHPUnit, debugging, logging, profiling | "test", "debug", "error", "bug", "log", "problema", "no funciona" |
| `orchestrator` | Coordina subagentes para tareas complejas — flujos completos de desarrollo | "crear módulo completo", "CRUD completo", "implementar desde cero", "orquestar" |
| `contable-rd` | Especialista contable RD — DGII, NCF, ITBIS, retenciones, cuentas, normativas fiscales dominicanas | "contable", "DGII", "NCF", "ITBIS", "comprobante", "tributario", "impuesto", "contabilidad", "retención" |
| `software-architect` | Arquitecto de Software Senior — diseño, patrones, escalabilidad, modelado de datos, trade-offs técnicos | "arquitectura", "diseño", "patron", "escala", "refactor", "estructura", "modelo de datos", "relaciones", "decision tecnica", "trade-off", "DDD", "CQRS" |
| `qa-engineer` | Ingeniero QA — pruebas funcionales, integración, validaciones ERP, detección de errores, control de calidad | "test", "QA", "prueba", "validación", "bug", "regresión", "caso de prueba", "coverage" |
| `erp-analyst` | Analista funcional ERP — procesos empresariales, levantamiento de requerimientos, flujos de facturación, inventario, contabilidad | "requerimiento", "proceso", "flujo", "negocio", "user story", "análisis funcional", "levantamiento" |
| `analista-negocio` | Senior Business Analyst — descubre procesos, actores, reglas, excepciones, documentos, permisos, KPIs, integraciones, automatización | "analista negocio", "analista-negocio", "proceso", "regla negocio", "permisos", "flujos", "KPI", "automatización", "documentos", "actores", "requisitos funcionales", "matriz permisos" |
| `security-expert` | Especialista en seguridad web — Laravel Security, autenticación, autorización, OWASP, protección de APIs, auditoría | "seguridad", "auth", "permiso", "OWASP", "vulnerabilidad", "auditoría", "encriptación", "protección" |

## Ubicación

Los agentes están en `.opencode/agent/`:
```
.opencode/agent/
├── backend.md
├── contable-rd.md
├── database-expert.md
├── erp-analyst.md
├── analista-negocio.md
├── frontend.md
├── orchestrator.md
├── qa-engineer.md
├── security-expert.md
├── software-architect.md
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
@database-expert agrega una columna a la tabla de facturas
@testing depura el error en ventas
@contable-rd explica el proceso de retencion ITBIS
@software-architect diseña la arquitectura del módulo de reportes
@qa-engineer crea pruebas para el flujo de facturación
@erp-analyst levanta requerimientos del módulo de nómina
@analista-negocio analiza el flujo completo de facturación
@security-expert audita la protección de APIs
```

### Flujo Completo
Para crear un módulo completo, simplemente describe lo que necesitas:
```
Crear módulo de categorías con CRUD completo
```
El `orchestrator` coordinará database → backend → frontend → testing automáticamente.
