# new-business-type

Generate a complete new business type (negocio) with all associated models, controllers, views, migrations, seeders, routes, permissions, and roles.

## Trigger Keywords

"crear tipo de negocio", "nuevo negocio", "business type", "nueva modalidad", "veterinaria", "gimnasio", "peluqueria", "academia", "clinica", "hotel", "farmacia", "petshop"

## Workflow

When the user wants to create a new business type, follow these steps:

### Step 1: Gather Requirements

Ask the user for:
1. **Nombre del negocio** (display name) — e.g., "Veterinaria"
2. **Slug** (machine-readable) — e.g., "veterinaria"
3. **Descripción corta** — e.g., "Clínica veterinaria y tienda de mascotas"
4. **Icono Bootstrap Icons** — e.g., "bi-heart-pulse"
5. **Color Bootstrap** — e.g., "danger"
6. **Entidades principales del dominio** — Ask for a list of 3-8 entities that are unique to this business type. For each entity, describe:
   - Entity name (singular, PascalCase)
   - Fields (name, type, nullable)
   - Relationships to other entities
7. **Roles específicos** — e.g., "veterinario", "recepcionista", "peluquero-canino"
8. **Módulos genéricos a incluir** — From the existing modules list (dashboard, inventario, ventas, clientes, etc.)

### Step 2: Generate Business Type Entry

Create/update the business type record:

```bash
# Create the business type row in database
php artisan tinker --execute="
\$bt = \App\Models\BusinessType::create([
    'slug' => '{SLUG}',
    'key' => '{SLUG}',
    'nombre' => '{NOMBRE}',
    'descripcion' => '{DESCRIPCION}',
    'color' => '{COLOR}',
    'icon' => '{ICON}',
    'activo' => true,
    'orden' => {ORDEN},
]);
foreach ({MODULOS_ARRAY} as \$i => \$m) {
    \App\Models\BusinessTypeModule::create([
        'business_type_id' => \$bt->id,
        'modulo_key' => \$m,
        'visible' => true,
        'orden' => \$i,
    ]);
}
\App\Models\BusinessType::flush();
echo 'Business type created: ' . \$bt->slug;\n";
```

### Step 3: Generate Domain Entities

For EACH domain entity described by the user, generate:

#### 3a. Migration

Template: `templates/migration/entity_migration.stub`

Rules:
- Table name: snake_case plural (e.g., `pacientes`, `citas`)
- Columns: `id`, `tenant_id`, + entity fields + `softDeletes()` + timestamps
- Foreign key: `foreignId('tenant_id')->constrained('business_instances')->cascadeOnDelete()`
- Index: `index('tenant_id')`
- Place in: `database/migrations/{timestamp}_create_{snake_plural}_table.php`

#### 3b. Model

Template: `templates/model/entity_model.stub`

Rules:
- Namespace: `App\Models`
- Traits: `HasFactory, TenantScope`
- Fillable: all fields except `tenant_id`
- Casts: dates, decimals, booleans as appropriate
- Relationships: define `belongsTo`/`hasMany` for each relationship
- Accessor: `attributeLabelAttribute()` for display name if applicable

Place in: `app/Models/{Entity}.php`

#### 3c. Controller Web

Template: `templates/controller-web/entity_controller.stub`

Rules:
- Namespace: `App\Http\Controllers`
- Middleware: `auth`, `role:{role}` (or `auth` for basic)
- Resourceful methods: index, create, store, show, edit, update, destroy
- Search, paginate, authorize actions
- Use `TenantScope` via model
- Flash messages in Spanish
- Redirect to `index` after store/update, `back()` after destroy

Place in: `app/Http/Controllers/{Entity}Controller.php`

#### 3d. Controller API

Template: `templates/controller-api/entity_api_controller.stub`

Rules:
- Namespace: `App\Http\Controllers\Api`
- Extends: `Controller`
- Methods: index, store, show, update, destroy
- Validation in each method
- Return JSON responses with standard format
- Auto-assign `tenant_id` from `auth()->user()->business_instance_id`

Place in: `app/Http/Controllers/Api/{Entity}ApiController.php`

#### 3e. Views (Blade)

Templates: `templates/view/{entity}_{view}.stub`

Generate these views:
- `index.blade.php` — DataTables table with search, pagination, columns
- `create.blade.php` — Form with fields
- `edit.blade.php` — Pre-filled form
- `show.blade.php` — Detail view with relationships

Place in: `resources/views/{snake_plural}/`

#### 3f. Routes

Add routes to `routes/web.php` and `routes/api.php`:

Web routes (inside a group with `auth` middleware):
```php
Route::resource('{snake_plural}', {Entity}Controller::class);
```

API routes (inside a group with `auth:sanctum` middleware):
```php
Route::apiResource('{snake_plural}', {Entity}ApiController::class);
```

#### 3g. Seeder

Template: `templates/seeder/entity_seeder.stub`

Rules:
- Class: `{Entity}Seeder`
- Seeds 5-10 sample records
- Place in: `database/seeders/{Entity}Seeder.php`
- Include in `DatabaseSeeder::call()`

### Step 4: Update Configuration Files

#### 4a. business_type_roles.php

Append the new business type roles:

```php
'{SLUG}' => [
    'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', '{CUSTOM_ROLES}', 'instance-admin'],
    'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', '{CUSTOM_ROLES}', 'instance-admin'],
    'admin-business' => ['gerente', 'supervisor', 'administrativo', '{CUSTOM_ROLES}', 'instance-admin'],
],
```

#### 4b. PermissionSeeder.php

Add permissions for the new module:

```php
'{MODULE_KEY}' => [
    '{module_key}.view',
    '{module_key}.create',
    '{module_key}.edit',
    '{module_key}.delete',
],
```

Then add permissions to the `admin-business` and `gerente` role arrays.

#### 4c. BusinessTypeSeeder.php

Add the new business type entry to the `$tipos` array with all module keys.

#### 4d. OwnerController.php — cleanInstance()

Add the new table names to the `$tables` array in `cleanInstance()` method.

### Step 5: Run Migrations and Seeders

```bash
php artisan migrate
php artisan db:seed --class={Entity}Seeder
php artisan cache:forget business_types_all
```

### Step 6: Verify

- Visit the web routes to confirm CRUD works
- Check that the business type appears in the owner panel
- Verify permissions are correctly assigned

## Templates Reference

All templates are in `.opencode/skills/new-business-type/templates/`:

| Template | Purpose |
|----------|---------|
| `model/entity_model.stub` | Eloquent model with TenantScope |
| `controller-web/entity_controller.stub` | Web CRUD controller |
| `controller-api/entity_api_controller.stub` | API CRUD controller |
| `migration/entity_migration.stub` | Database migration |
| `view/index.stub` | Index view with DataTables |
| `view/create.stub` | Create form view |
| `view/edit.stub` | Edit form view |
| `view/show.stub` | Show detail view |
| `seeder/entity_seeder.stub` | Sample data seeder |
| `service/entity_service.stub` | Domain logic service (optional) |
| `resource/entity_resource.stub` | API resource transformer (optional) |

## Naming Conventions

| Concept | Convention | Example |
|---------|-----------|---------|
| Entity class | PascalCase singular | `Paciente` |
| Table name | snake_case plural | `pacientes` |
| Controller | PascalCase + "Controller" | `PacienteController` |
| View folder | snake_case plural | `resources/views/pacientes/` |
| Relationship method | snake_case | `belongToCliente()` |
| Foreign key | snake_case + `_id` | `cliente_id` |
| Route resource | snake_case plural | `Route::resource('pacientes', ...)` |
| Permission | snake_case + dot notation | `pacientes.view` |
| Role name | snake_case | `veterinario` |
| Business type slug | lowercase + hyphens | `veterinaria` |

## Common Patterns

### Model with TenantScope

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\TenantScope;

class {Entity} extends Model
{
    use HasFactory, TenantScope;

    protected $fillable = [
        'tenant_id', '{fields...}',
    ];

    protected $casts = [
        '{dates...} => datetime',
        '{decimals...} => decimal:2',
    ];

    public function {relationship}(): BelongsTo
    {
        return $this->belongsTo({RelatedModel}::class);
    }
}
```

### Controller Web Pattern

```php
<?php
namespace App\Http\Controllers;

use App\Models\{Entity};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class {Entity}Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $query = {Entity}::query();
        
        if ($search = request('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $items = $query->latest()->paginate(15)->withQueryString();
        return view('{plural}.index', compact('items'));
    }

    public function create()
    {
        return view('{plural}.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'field' => 'required|string|max:255',
        ]);

        {Entity}::create(array_merge($validated, [
            'tenant_id' => auth()->user()->business_instance_id,
        ]));

        return redirect()->route('{plural}.index')
            ->with('success', '{Entity} creado correctamente.');
    }

    public function show({Entity} $item)
    {
        return view('{plural}.show', compact('item'));
    }

    public function edit({Entity} $item)
    {
        return view('{plural}.edit', compact('item'));
    }

    public function update(Request $request, {Entity} $item)
    {
        $validated = $request->validate([...]);
        $item->update($validated);
        return redirect()->route('{plural}.index')
            ->with('success', '{Entity} actualizado correctamente.');
    }

    public function destroy({Entity} $item)
    {
        $item->delete();
        return back()->with('success', '{Entity} eliminado.');
    }
}
```

### Migration Pattern

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{table}', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('business_instances')->cascadeOnDelete();
            $table->index('tenant_id');
            // fields...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{table}');
    }
};
```

## Important Reminders

1. **Always use TenantScope** — every domain model must use the `TenantScope` trait
2. **Auto-assign tenant_id** — never accept `tenant_id` from user input, assign from `auth()->user()->business_instance_id`
3. **Spanish flash messages** — all success/error messages in Spanish
4. **Bootstrap Icons** — use Bootstrap Icons for view icons
5. **DataTables** — index views must use DataTables for search/pagination
6. **Premium UI** — apply the premium glassmorphism UI pattern (see `premium-ui` skill)
7. **Flush cache** — always call `BusinessType::flush()` after creating/updating business types
8. **Clean instance** — add new tables to `OwnerController::cleanInstance()` cleanup list
9. **Permission naming** — use `{module_key}.{action}` format (view, create, edit, delete)
10. **Role mapping** — add custom roles to all three levels (owner, root, admin-business) in `business_type_roles.php`

## Integration with Other Skills

- Use `datatable-ui` skill after generating views to add DataTables
- Use `premium-ui` skill after generating views to apply premium styling
- Use this skill BEFORE adding routes — the generated controllers must exist first
