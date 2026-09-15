# Setup Wizard Skill

## Triggers
Load this skill automatically when the user mentions ANY of:
- "crear módulo", "nuevo módulo", "module:create"
- "registrar módulo", "agregar módulo"
- "crear entidad", "nueva entidad" (en contexto de módulo)
- "php artisan module:create"

## Architecture

### Tables
- `wizard_steps` — config for each wizard step (key, module_key, label, icon, required, skipable, entity_class, orden)
- `business_instances.setup_completed` — per-instance flag

### Models with wizard steps (in `config/wizard.php`)
- `Sucursal`, `Caja`, `Almacen`, `Producto`, `NcfSequence`
- `MesaUbicacion`, `MesaCategoria`, `Mesa` (restaurante)
- `LavaderoServicio`, `Lavador` (lavadero)

### Filter
Steps shown = intersection of:
- `Module keys in admin's InstanceRole (visibleModules)`
- `Module keys in BusinessType (getModulosVisibles)` OR system modules (ncf)

### Middleware `CheckSetupWizard`
- Runs on every web request (appended to `web` group)
- Redirects to `GET /setup/wizard` if:
  - User is NOT owner/root
  - Route is NOT setup.* or logout
  - `businessInstance->setup_completed === false`
  - `instanceRole->name === 'admin'`

## How to create a new module with wizard step

### Method 1: Interactive command (recommended)
```
php artisan module:create
```
Follow the prompts. It automatically:
- Creates model, migration, controller, views, routes
- Adds to `ModuloSeeder`, `PermissionSeeder`
- Adds to `config/wizard.php`
- Runs `php artisan wizard:sync`

### Method 2: Manual
1. Create model with `HasWizardStep` trait
2. Implement `wizardStepConfig()` on the model
3. Add model class to `config/wizard.php`
4. Run `php artisan wizard:sync`

### After creating
```
php artisan migrate
php artisan db:seed --class=ModuloSeeder
php artisan db:seed --class=PermissionSeeder
```

## Commands reference
- `php artisan module:create` — interactive module creator
- `php artisan wizard:sync` — sync wizard steps from config/wizard.php
