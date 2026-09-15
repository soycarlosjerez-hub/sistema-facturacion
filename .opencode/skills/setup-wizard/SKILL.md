# Setup Wizard Skill

## Triggers
"crear modulo", "nuevo modulo", "module:create", "registrar modulo", "php artisan module:create"

## Arquitectura
- `wizard_steps` — config de pasos (key, module_key, label, icon, required, skipable, entity_class, orden)
- `business_instances.setup_completed` — flag por instancia
- Middleware `CheckSetupWizard` — redirige a `/setup/wizard` si `setup_completed === false`

## Comandos
- `php artisan module:create` — interactivo (modelo, migracion, controlador, vistas, rutas, seeders, permisos, wizard)
- `php artisan wizard:sync` — sincroniza pasos desde config/wizard.php

## Crear nuevo modulo con wizard
1. Crear modelo con `HasWizardStep` + `wizardStepConfig()`
2. Agregar a `config/wizard.php`
3. `php artisan wizard:sync`

## After creating
```
php artisan migrate
php artisan db:seed --class=ModuloSeeder
php artisan db:seed --class=PermissionSeeder
```
