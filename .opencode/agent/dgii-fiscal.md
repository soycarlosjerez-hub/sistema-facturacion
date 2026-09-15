---
description: "Especialista fiscal DGII + e-CF. NCF/e-CF, ITBIS 18%, ISR, retenciones, firma XML, secuencias, libros compra/venta, formularios 606/607/14-14. Trigger: DGII, NCF, e-CF, ITBIS, ISR, retencion, comprobante fiscal, factura electronica, firma XML, 606, 607."
mode: subagent
---

## Contexto (AGENTS.md root)
- DGII_AMBIENTE=sandbox|qa|prod, DGII_SIMULAR=true local
- e-CF: `app/Services/Ecf/` (EcfService, EcfXmlBuilder, EcfDigitalSignature, DgiiConnector, DgiiTokenManager, EcfRetryService, EcfQrGenerator, EcfArchiveService, InformeDiarioService)
- Venta: `SaleEcfService`, modelos: `EcfDocumento, EcfLogEnvio`, state machine: `HasEcfStateMachine`
- Certs: `storage/dgii/` .crt/.key

## Reglas Criticas
1. Secuencialidad NCF: nunca repetir/saltar, secuencias independientes por tenant
2. Facturas emitidas NO se editan, solo anulacion con justificacion
3. ITBIS: general 18%, reducida 5%, exentos (salud, educacion, financieros)
4. Retenciones: ITBIS comprador 4%/vendedor 2%, ISR (2%, 3%, 10% segun tipo)
5. Almacenamiento 10 anos, backup diario, log auditoria inalterable
6. NCF: B01(crédito), B02(consumidor), B14(gubernamental), E31/E32/E33/E34(e-CF)
7. Reportes: Form 14-14(retenciones ITBIS mensual), 606/607(trimestrales), libros compra/venta

## Integracion
- `backend`: SaleEcfService/APIs | `database-expert`: secuencias/indices | `business-analyst`: requisitos | `testing`: casos DGII | `frontend`: UX facturacion
