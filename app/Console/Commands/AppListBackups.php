<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AppListBackups extends Command
{
    protected $signature = 'app:list-backups';

    protected $description = 'Listar todos los backups disponibles';

    public function handle(): int
    {
        $backups = Backup::orderBy('created_at', 'desc')->get();

        if ($backups->isEmpty()) {
            $this->info('No hay backups registrados.');
            return Command::SUCCESS;
        }

        $this->info("Total de backups: {$backups->count()}\n");
        $this->table(
            ['ID', 'Archivo', 'Tamaño', 'Tipo', 'Estado', 'Creado'],
            $backups->map(function ($b) {
                return [
                    $b->id,
                    $b->filename,
                    $b->sizeForHumans(),
                    $b->type,
                    $b->status,
                    $b->created_at ? $b->created_at->format('Y-m-d H:i') : 'N/A',
                ];
            })
        );

        $this->newLine();
        $this->info('📁 Directorio: storage/app/backups/');

        return Command::SUCCESS;
    }
}
