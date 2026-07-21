<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExportApiDocs extends Command
{
    protected $signature = 'api-docs:export
        {--format=md : md (markdown) o json}
        {--output= : Ruta del archivo de salida (default: docs/api-export.md o .json)}
        {--no-base : Omitir archivos base (README, authentication, response-format)}';

    protected $description = 'Exporta toda la documentación de la API en un solo archivo para IA';

    public function handle(): int
    {
        $docsDir = base_path('docs/api');
        $modulesDir = $docsDir . '/modules';
        $format = $this->option('format');
        $output = $this->option('output')
            ?? base_path("docs/api-export.{$format}");
        $includeBase = !$this->option('no-base');

        if (!is_dir($modulesDir)) {
            $this->error("No se encontró el directorio: {$modulesDir}");
            return Command::FAILURE;
        }

        if ($format === 'json') {
            $this->exportJson($docsDir, $modulesDir, $includeBase, $output);
        } else {
            $this->exportMarkdown($docsDir, $modulesDir, $includeBase, $output);
        }

        $this->info("Exportación completada: {$output}");
        return Command::SUCCESS;
    }

    private function exportMarkdown(string $docsDir, string $modulesDir, bool $includeBase, string $output): void
    {
        $lines = [];
        $lines[] = '# API Documentation — Export';
        $lines[] = '';
        $lines[] = '> Exportado el ' . now()->format('Y-m-d H:i:s');
        $lines[] = '>';
        $lines[] = '> Total de módulos: ' . count(glob($modulesDir . '/*.md'));
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '';

        if ($includeBase) {
            foreach (['README.md', 'authentication.md', 'response-format.md'] as $baseFile) {
                $path = $docsDir . '/' . $baseFile;
                if (file_exists($path)) {
                    $this->line("  → Agregando {$baseFile}...");
                    $lines[] = file_get_contents($path);
                    $lines[] = '';
                    $lines[] = '---';
                    $lines[] = '';
                }
            }
        }

        $files = collect(scandir($modulesDir))
            ->filter(fn($f) => pathinfo($f, PATHINFO_EXTENSION) === 'md')
            ->sort()
            ->values();

        foreach ($files as $filename) {
            $this->line("  → Agregando modules/{$filename}...");
            $lines[] = file_get_contents($modulesDir . '/' . $filename);
            $lines[] = '';
            $lines[] = '---';
            $lines[] = '';
        }

        file_put_contents($output, implode("\n", $lines));
    }

    private function exportJson(string $docsDir, string $modulesDir, bool $includeBase, string $output): void
    {
        $data = [
            '_meta' => [
                'exported_at' => now()->toIso8601String(),
                'total_modules' => count(glob($modulesDir . '/*.md')),
            ],
        ];

        if ($includeBase) {
            foreach (['README.md', 'authentication.md', 'response-format.md'] as $baseFile) {
                $path = $docsDir . '/' . $baseFile;
                if (file_exists($path)) {
                    $this->line("  → Agregando {$baseFile}...");
                    $key = pathinfo($baseFile, PATHINFO_FILENAME);
                    $data['base'][$key] = file_get_contents($path);
                }
            }
        }

        $files = collect(scandir($modulesDir))
            ->filter(fn($f) => pathinfo($f, PATHINFO_EXTENSION) === 'md')
            ->sort()
            ->values();

        foreach ($files as $filename) {
            $this->line("  → Agregando modules/{$filename}...");
            $moduleName = ucwords(implode(' ', explode('-', basename($filename, '.md'))));
            $data['modules'][$moduleName] = [
                'filename' => $filename,
                'content' => file_get_contents($modulesDir . '/' . $filename),
            ];
        }

        file_put_contents($output, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
