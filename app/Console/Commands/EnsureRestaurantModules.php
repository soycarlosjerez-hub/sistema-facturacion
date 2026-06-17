<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use App\Models\Modulo;

class EnsureRestaurantModules extends Command
{
    protected $signature = 'ensure:restaurant-modules';
    protected $description = 'Create or update the "restaurante" BusinessType and ensure its modules are present and visible';

    public function handle()
    {
        // Find or create the business type
        $businessType = BusinessType::firstOrCreate(
            ['slug' => 'restaurante'],
            [
                'key' => 'restaurante',
                'nombre' => 'Restaurante / Bar / Café',
                'descripcion' => 'Tipo de negocio para restaurantes',
                'color' => 'primary',
                'icon' => 'bi-cup-straw',
                'activo' => true,
                'orden' => 0,
                'campos_extra' => [],
                'soft_delete_default' => false,
            ]
        );

        // Ensure the business type is active
        if (! $businessType->activo) {
            $businessType->activo = true;
            $businessType->save();
        }

        // Get all modules (active ones)
        $allModules = Modulo::where('activo', true)->get();

        foreach ($allModules as $module) {
            BusinessTypeModule::updateOrCreate(
                [
                    'business_type_id' => $businessType->id,
                    'modulo_key' => $module->key,
                ],
                [
                    'visible' => true,
                    'orden' => $module->orden ?? 0,
                ]
            );
        }

        // Flush the BusinessType cache so changes are visible immediately
        BusinessType::flush();

        $this->info('Restaurant business type and modules ensured.');
        return 0;
    }
}
?>
