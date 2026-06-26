<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleCreateCommand extends Command
{
    protected $signature = 'module:create';
    protected $description = 'Create a new module with model, controller, views, routes, permissions, and wizard step';

    public function handle(): int
    {
        $moduleKey = $this->ask('Module key (e.g. talleres)');
        $moduleLabel = $this->ask('Module label (e.g. Talleres)');
        $icon = $this->ask('Icon (bi-*)', 'bi-folder');
        $category = $this->choice('Category', ['core', 'operaciones', 'clientes', 'organizacion', 'lavadero', 'restaurante', 'reportes', 'sistema', 'configuracion'], 'operaciones');

        $hasEntity = $this->confirm('Has main entity?', true);
        $entityName = null;
        $wizardStep = false;
        $wizardRequired = true;
        $wizardSkipable = false;

        if ($hasEntity) {
            $entityName = $this->ask('Entity name (singular, e.g. Taller)');
            $tableName = Str::snake(Str::pluralStudly($entityName));
            $fields = $this->ask('Entity fields (comma separated, e.g. nombre:string, precio:decimal, activo:boolean)', 'nombre:string');
            $wizardStep = $this->confirm('Has wizard step?', $entityName !== null);

            if ($wizardStep) {
                $wizardRequired = $this->confirm('Wizard step required?', true);
                if (!$wizardRequired) {
                    $wizardSkipable = $this->confirm('Wizard step skipable?', true);
                }
            }
        }

        $singular = $entityName ? Str::studly($entityName) : Str::studly($moduleKey);
        $plural = Str::pluralStudly($singular);
        $snakePlural = Str::snake($plural);
        $snakeSingular = Str::snake($singular);
        $kebabPlural = Str::kebab($plural);
        $routePrefix = $kebabPlural;
        $permissionPrefix = $snakeSingular;
        $modelNamespace = "App\\Models\\{$singular}";
        $controllerNamespace = "App\\Http\\Controllers\\{$singular}Controller";
        $viewsDir = resource_path("views/{$snakePlural}");

        // Permission names
        $permissions = [
            "{$permissionPrefix}.view",
            "{$permissionPrefix}.create",
            "{$permissionPrefix}.edit",
            "{$permissionPrefix}.delete",
        ];

        // 1. Create migration
        $migrationPath = database_path("migrations/" . date('Y_m_d_His') . "_create_{$snakePlural}_table.php");
        $migrationStub = $this->getMigrationStub($snakePlural, $singular, $fields);
        File::put($migrationPath, $migrationStub);
        $this->info("Migration created: {$migrationPath}");

        // 2. Create model
        $modelPath = app_path("Models/{$singular}.php");
        $modelStub = $this->getModelStub($singular, $snakePlural, $wizardStep, $wizardRequired, $wizardSkipable, $moduleKey);
        File::put($modelPath, $modelStub);
        $this->info("Model created: {$modelPath}");

        // 3. Create controller
        $controllerPath = app_path("Http/Controllers/{$singular}Controller.php");
        $controllerStub = $this->getControllerStub($singular, $plural, $snakePlural, $permissionPrefix, $routePrefix);
        File::put($controllerPath, $controllerStub);
        $this->info("Controller created: {$controllerPath}");

        // 4. Create views
        File::makeDirectory($viewsDir, 0755, true, true);
        $this->createView($viewsDir, 'index', $singular, $plural, $snakeSingular, $snakePlural, $permissionPrefix, $routePrefix);
        $this->createView($viewsDir, 'create', $singular, $plural, $snakeSingular, $snakePlural, $permissionPrefix, $routePrefix);
        $this->createView($viewsDir, 'edit', $singular, $plural, $snakeSingular, $snakePlural, $permissionPrefix, $routePrefix);
        $this->createView($viewsDir, 'show', $singular, $plural, $snakeSingular, $snakePlural, $permissionPrefix, $routePrefix);
        $this->info("Views created in: {$viewsDir}");

        // 5. Add to ModuloSeeder
        $this->addToModuloSeeder($moduleKey, $moduleLabel, $icon, $category, $routePrefix, $permissions[0]);
        $this->info("Added to ModuloSeeder");

        // 6. Add to PermissionSeeder
        $this->addToPermissionSeeder($moduleKey, $permissions);
        $this->info("Added to PermissionSeeder");

        // 7. Add to config/wizard.php
        if ($wizardStep && $hasEntity) {
            $this->addToWizardConfig($modelNamespace);
            $this->info("Added to config/wizard.php");
        }

        // 8. Add routes
        $this->info("Add these routes to routes/web.php:");
        $this->line($this->getRouteStub($routePrefix, $singular, $permissions));

        // 9. Sync wizard steps if applicable
        if ($wizardStep) {
            $this->call('wizard:sync');
        }

        $this->newLine();
        $this->info("Module '{$moduleLabel}' created successfully!");
        $this->warn("Don't forget to:");
        $this->warn("  1. Run: php artisan migrate");
        $this->warn("  2. Run: php artisan db:seed --class=ModuloSeeder");
        $this->warn("  3. Run: php artisan db:seed --class=PermissionSeeder");
        $this->warn("  4. Add routes to routes/web.php (see above)");
        $this->warn("  5. Assign module to InstanceRole from owner panel");

        return Command::SUCCESS;
    }

    protected function getMigrationStub(string $table, string $model, string $fields): string
    {
        $fieldLines = '';
        foreach (explode(',', $fields) as $field) {
            $parts = explode(':', trim($field));
            $name = $parts[0] ?? 'name';
            $type = $parts[1] ?? 'string';
            $fieldLines .= "            \$table->{$type}('{$name}');\n";
        }

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
{$fieldLines}            \$table->foreignId('tenant_id')->constrained('business_instances')->cascadeOnDelete();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};

PHP;
    }

    protected function getModelStub(string $model, string $table, bool $wizard, bool $wizardRequired, bool $wizardSkipable, string $moduleKey): string
    {
        $traitImport = $wizard ? "use App\\Traits\\HasWizardStep;\n" : '';
        $traitUse = $wizard ? "    use HasWizardStep;\n" : '';
        $wizardMethod = $wizard ? <<<PHP

    public static function wizardStepConfig(): ?array
    {
        return [
            'module_key' => '{$moduleKey}',
            'label' => '{$model}',
            'icon' => 'bi-folder',
            'required' => {$wizardRequired},
            'skipable' => {$wizardSkipable},
            'orden' => 100,
        ];
    }
PHP : '';

        return <<<PHP
<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
{$traitImport}
class {$model} extends Model
{
    use TenantScope;
{$traitUse}
    protected \$table = '{$table}';

    protected \$fillable = [
        'tenant_id',
    ];
{$wizardMethod}
}

PHP;
    }

    protected function getControllerStub(string $model, string $plural, string $viewDir, string $permissionPrefix, string $routePrefix): string
    {
        $modelVar = lcfirst($model);
        return <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\\{$model};
use Illuminate\Http\Request;

class {$model}Controller extends Controller
{
    public function index()
    {
        \${$modelVar}s = {$model}::orderBy('created_at', 'desc')->paginate(10);
        return view('{$viewDir}.index', compact('{$modelVar}s'));
    }

    public function create()
    {
        return view('{$viewDir}.create');
    }

    public function store(Request \$request)
    {
        \$data = \$request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        \$data['tenant_id'] = auth()->user()->business_instance_id;
        {$model}::create(\$data);
        return redirect()->route('{$routePrefix}.index')->with('success', '{$model} creado correctamente.');
    }

    public function show({$model} \${$modelVar})
    {
        return view('{$viewDir}.show', compact('{$modelVar}'));
    }

    public function edit({$model} \${$modelVar})
    {
        return view('{$viewDir}.edit', compact('{$modelVar}'));
    }

    public function update(Request \$request, {$model} \${$modelVar})
    {
        \$data = \$request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        \${$modelVar}->update(\$data);
        return redirect()->route('{$routePrefix}.index')->with('success', '{$model} actualizado correctamente.');
    }

    public function destroy({$model} \${$modelVar})
    {
        \${$modelVar}->delete();
        return redirect()->route('{$routePrefix}.index')->with('success', '{$model} eliminado correctamente.');
    }
}

PHP;
    }

    protected function createView(string $dir, string $view, string $model, string $plural, string $snakeSingular, string $snakePlural, string $permissionPrefix, string $routePrefix): void
    {
        $stub = match ($view) {
            'index' => $this->getIndexViewStub($model, $plural, $snakeSingular, $snakePlural, $routePrefix),
            'create' => $this->getFormViewStub($model, $plural, $snakePlural, $routePrefix, 'create'),
            'edit' => $this->getFormViewStub($model, $plural, $snakePlural, $routePrefix, 'edit'),
            'show' => $this->getShowViewStub($model, $snakeSingular, $routePrefix),
            default => '',
        };
        File::put("{$dir}/{$view}.blade.php", $stub);
    }

    protected function getIndexViewStub(string $model, string $plural, string $snakeSingular, string $snakePlural, string $routePrefix): string
    {
        $modelVar = lcfirst($model);
        return <<<BLADE
@extends('layouts.app')
@section('title', '{$plural}')
@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">{$plural}</h2>
        <a href="{{ route('{$routePrefix}.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg"></i> Nuevo
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\${$modelVar}s as \${$modelVar})
                    <tr>
                        <td>{{\${$modelVar}->id}}</td>
                        <td>{{\${$modelVar}->nombre}}</td>
                        <td>
                            <a href="{{ route('{$routePrefix}.edit', \${$modelVar}) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('{$routePrefix}.destroy', \${$modelVar}) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ \${$modelVar}s->links() }}
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getFormViewStub(string $model, string $plural, string $snakePlural, string $routePrefix, string $action): string
    {
        $isEdit = $action === 'edit';
        $modelVar = lcfirst($model);
        $route = $isEdit ? "route('{$routePrefix}.update', \${$modelVar})" : "route('{$routePrefix}.store')";
        $method = $isEdit ? '@method("PUT")' : '';
        $nameValue = $isEdit ? "{{\${$modelVar}->nombre}}" : "{{ old('nombre') }}";
        $actionLabel = $isEdit ? 'Editar' : 'Crear';

        return <<<BLADE
@extends('layouts.app')
@section('title', '{$actionLabel} {$model}')
@section('content')
<div class="container-fluid px-4 py-3">
    <h2 class="fw-bold mb-3">{$actionLabel} {$model}</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ {$route} }}" method="POST">
                @csrf
                {$method}
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="{$nameValue}" required>
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-save"></i> {$actionLabel}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getShowViewStub(string $model, string $snakeSingular, string $routePrefix): string
    {
        return '<div></div>';
    }

    protected function getRouteStub(string $prefix, string $model, array $permissions): string
    {
        return <<<PHP
    // {$model}
    Route::get('/{$prefix}', [{$model}Controller::class, 'index'])->name('{$prefix}.index')->middleware('permission:{$permissions[0]}');
    Route::get('/{$prefix}/create', [{$model}Controller::class, 'create'])->name('{$prefix}.create')->middleware('permission:{$permissions[1]}');
    Route::post('/{$prefix}', [{$model}Controller::class, 'store'])->name('{$prefix}.store')->middleware('permission:{$permissions[1]}');
    Route::get('/{$prefix}/{{$model}}', [{$model}Controller::class, 'show'])->name('{$prefix}.show')->middleware('permission:{$permissions[0]}');
    Route::get('/{$prefix}/{{$model}}/edit', [{$model}Controller::class, 'edit'])->name('{$prefix}.edit')->middleware('permission:{$permissions[2]}');
    Route::put('/{$prefix}/{{$model}}', [{$model}Controller::class, 'update'])->name('{$prefix}.update')->middleware('permission:{$permissions[2]}');
    Route::delete('/{$prefix}/{{$model}}', [{$model}Controller::class, 'destroy'])->name('{$prefix}.destroy')->middleware('permission:{$permissions[3]}');
PHP;
    }

    protected function addToModuloSeeder(string $key, string $label, string $icon, string $category, string $routePrefix, string $permission): void
    {
        $seederPath = database_path('seeders/ModuloSeeder.php');
        if (!File::exists($seederPath)) return;

        $content = File::get($seederPath);
        $entry = "\n            // {$label}\n";
        $entry .= "            ['key' => '{$key}', 'label' => '{$label}', 'icon' => '{$icon}', 'categoria' => '{$category}', 'orden' => 99,\n";
        $entry .= "             'sidebar_route' => '{$routePrefix}.index', 'sidebar_is_route' => '{$routePrefix}.*', 'sidebar_exact_route' => '{$routePrefix}.index', 'sidebar_permission' => '{$permission}'],\n";

        $content = str_replace('];', $entry . '        ];', $content);
        File::put($seederPath, $content);
    }

    protected function addToPermissionSeeder(string $moduleKey, array $permissions): void
    {
        $seederPath = database_path('seeders/PermissionSeeder.php');
        if (!File::exists($seederPath)) return;

        $content = File::get($seederPath);
        $entry = "            '{$moduleKey}' => [\n";
        foreach ($permissions as $p) {
            $entry .= "                '{$p}',\n";
        }
        $entry .= "            ],\n";

        $insertPos = strpos($content, "'dashboard' => [");
        if ($insertPos !== false) {
            $insertPos = strpos($content, "];", $insertPos) + 2;
            $content = substr($content, 0, $insertPos) . "\n" . $entry . substr($content, $insertPos);
        }
        File::put($seederPath, $content);
    }

    protected function addToWizardConfig(string $modelClass): void
    {
        $configPath = config_path('wizard.php');
        if (!File::exists($configPath)) return;

        $content = File::get($configPath);
        $indent = '    ';
        $entry = "        {$indent}\\{$modelClass}::class,\n";
        $content = str_replace('];', $entry . '    ];', $content);
        File::put($configPath, $content);
    }
}
