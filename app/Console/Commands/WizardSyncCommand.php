<?php

namespace App\Console\Commands;

use App\Models\WizardStep;
use Illuminate\Console\Command;

class WizardSyncCommand extends Command
{
    protected $signature = 'wizard:sync';
    protected $description = 'Synchronize wizard steps from config/wizard.php into the wizard_steps table';

    public function handle(): int
    {
        $models = config('wizard.models', []);
        $bar = $this->output->createProgressBar(count($models));
        $bar->start();

        $synced = 0;
        foreach ($models as $modelClass) {
            if (!class_exists($modelClass)) {
                $this->newLine();
                $this->warn("Class {$modelClass} does not exist, skipping.");
                $bar->advance();
                continue;
            }

            if (!method_exists($modelClass, 'wizardStepConfig')) {
                $bar->advance();
                continue;
            }

            $config = $modelClass::wizardStepConfig();
            if (!$config) {
                $bar->advance();
                continue;
            }

            $config['key'] = $config['key'] ?? str(class_basename($modelClass))->lower()->value();
            $config['entity_class'] = $modelClass;

            WizardStep::updateOrCreate(['key' => $config['key']], $config);
            $synced++;
            $bar->advance();
        }
        $bar->finish();

        $this->newLine();
        $this->info("Wizard steps synced: {$synced}");

        return Command::SUCCESS;
    }
}
