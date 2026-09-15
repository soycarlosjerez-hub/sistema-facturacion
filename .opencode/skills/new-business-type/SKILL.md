---
name: new-business-type
description: "Crear tipo de negocio completo: models, controllers, views, migrations, seeders, routes, permissions, roles. Trigger: 'crear tipo de negocio', 'nuevo negocio', 'business type', 'nueva modalidad'."
---

# New Business Type Skill

## Trigger
"crear tipo de negocio", "nuevo negocio", "business type", "nueva modalidad"

## Workflow

### Step 1: Recopilar
1. Nombre, slug, descripcion, icono Bootstrap, color Bootstrap
2. Entidades del dominio (3-8): entity name (PascalCase), fields, relaciones
3. Roles especificos y modulos genericos (dashboard, inventario, ventas, clientes)

### Step 2: Crear Business Type
```bash
php artisan tinker --execute="
\$bt = \App\Models\BusinessType::create(['slug'=>'SLUG','key'=>'SLUG','nombre'=>'NOMBRE','descripcion'=>'DESC','color'=>'COLOR','icon'=>'ICON','activo'=>true,'orden'=>ORDEN]);
foreach(MODULOS as \$i => \$m) \App\Models\BusinessTypeModule::create(['business_type_id'=>\$bt->id,'modulo_key'=>\$m,'visible'=>true,'orden'=>\$i]);
\$bt->flush();
echo 'OK: '.\$bt->slug;
"
```

### Step 3: Generar Entidades (c/p .stub templates en templates/)
- **Migration**: `snake_case` plural, `tenant_id` FK→`business_instances`, `index('tenant_id')`, `softDeletes()`
- **Model**: `HasFactory, TenantScope`, fillables, casts, relaciones `BelongsTo`/`HasMany`
- **Controller Web**: Resourceful methods, search+paginate, `auth` middleware, flash español, redirect/ back()
- **Controller API**: `auth:sanctum`, JSON responses, auto-assign `tenant_id`
- **Views**: index(DataTables)+create+edit+show en `resources/views/{snake_plural}/`
- **Routes**: `Route::resource()` en web.php + `Route::apiResource()` en api.php
- **Seeder**: 5-10 records

### Step 4: Configuracion
- **business_type_roles.php**: Agregar roles a owner/root/admin-business
- **PermissionSeeder**: `{modulo_key}.view/create/edit/delete` en admin-business y gerente
- **BusinessTypeSeeder**: Agregar a array `$tipos`
- **OwnerController**: Agregar tablas a `cleanInstance()`

### Step 5: Ejecutar
```
php artisan migrate
php artisan db:seed --class=EntitySeeder
php artisan cache:forget business_types_all
```

### Step 6: Verificar
- CRUD funciona, business type aparece en owner panel, permisos asignados

## Naming
Entity: PascalCase | Table: snake_case plural | Controller: PascalCase+Controller | Route: resource snake_plural | Permission: snake_case.action | Role: snake_case

## Templates en templates/
model/, controller-web/, controller-api/, migration/, view/ (index/create/edit/show), seeder/, service/, resource/

## Integracion
- `datatable-ui` + `premium-ui` AFTER generating views
- `backend` para logica compleja | `database-expert` para indices | `testing` para tests
