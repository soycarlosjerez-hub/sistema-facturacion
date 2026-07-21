<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiDocumentationController extends Controller
{
    public function index()
    {
        $docsDir = base_path('docs/api/modules');
        $modules = [];

        if (is_dir($docsDir)) {
            $files = collect(scandir($docsDir))
                ->filter(fn($f) => pathinfo($f, PATHINFO_EXTENSION) === 'md')
                ->sort()
                ->values();

            foreach ($files as $filename) {
                $filepath = $docsDir . DIRECTORY_SEPARATOR . $filename;
                $content = file_get_contents($filepath);

                $module = $this->parseModule($filename, $content);
                if ($module) {
                    $modules[] = $module;
                }
            }
        }

        $modules = collect($modules)->sortBy('name')->values()->all();

        return view('api.documentation', compact('modules'));
    }

    protected function parseModule(string $filename, string $content): ?array
    {
        $parts = explode('-', basename($filename, '.md'));
        $moduleName = ucwords(implode(' ', $parts));

        $lines = explode("\n", $content);
        $description = '';
        $foundTitle = false;
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;
            if (str_starts_with($trimmed, '# ')) {
                $foundTitle = true;
                continue;
            }
            if ($foundTitle && !str_starts_with($trimmed, '#') && !str_starts_with($trimmed, '---')) {
                $description = $trimmed;
                break;
            }
        }

        $sections = preg_split('/^## /m', $content);
        $endpoints = [];
        $fieldReferences = [];
        $notas = '';

        foreach ($sections as $section) {
            if (empty(trim($section))) continue;

            $secLines = explode("\n", $section);
            $heading = trim(array_shift($secLines));
            $body = implode("\n", $secLines);

            if (str_starts_with($heading, 'Endpoint ')) {
                $endpointName = trim(substr($heading, 9));
                $parsed = $this->parseEndpoint($endpointName, $body);
                if ($parsed) {
                    $endpoints[] = $parsed;
                }
            } elseif (str_starts_with($heading, 'Field Reference')) {
                $fieldReferences = $this->parseFieldReferences($body);
            } elseif ($heading === 'Notas') {
                $notas = trim($body);
            }
        }

        if (empty($endpoints) && empty($fieldReferences)) {
            return null;
        }

        return [
            'filename' => $filename,
            'name' => $moduleName,
            'description' => $description,
            'endpoints' => $endpoints,
            'field_references' => $fieldReferences,
            'notas' => $notas,
        ];
    }

    protected function parseEndpoint(string $name, string $body): ?array
    {
        $actionTitle = '';
        $method = '';
        $path = '';
        $summary = '';
        $permissions = '';
        $queryParams = [];
        $pathParams = [];
        $headers = '';
        $requestBodyJson = '';
        $campos = [];
        $validations = '';
        $responses = [];
        $exampleRequest = '';

        $lines = explode("\n", $body);
        $i = 0;
        $total = count($lines);

        // Extract H3 action title if present
        if ($i < $total && str_starts_with(trim($lines[$i]), '### ')) {
            $actionTitle = trim(str_replace('### ', '', trim($lines[$i])));
            $i++;
        }

        // Find method + path from `**`GET /api/...`**` pattern
        for ($j = $i; $j < $total; $j++) {
            $line = $lines[$j];
            if (preg_match('/\*\*`(GET|POST|PUT|PATCH|DELETE)\s+(\/.*?)`\*\*/', $line, $m)) {
                $method = $m[1];
                $path = $m[2];
                $i = $j + 1;
                break;
            }
            // Also try without code marks: **GET /api/...**
            if (preg_match('/\*\*(GET|POST|PUT|PATCH|DELETE)\s+(\/.*?)\*\*/', $line, $m)) {
                $method = $m[1];
                $path = $m[2];
                $i = $j + 1;
                break;
            }
        }

        // Process remaining lines through subsections
        $state = 'summary';
        $subsectionHeader = '';
        $captureJson = false;
        $captureCode = false;
        $capturedJson = '';
        $capturedCode = '';
        $inTable = false;
        $tableHeaderParsed = false;
        $tableRows = [];
        $currentTableType = '';

        for (; $i < $total; $i++) {
            $line = $lines[$i];
            $trimmed = trim($line);

            // Detect subsection headers like **Query Parameters:**
            if (preg_match('/^\*\*(.+?):\*\*\s*$/', $trimmed, $m)) {
                // Finalize previous subsection
                $this->finalizeSubsection($subsectionHeader, $tableRows, $capturedJson, $capturedCode, $inTable,
                    $queryParams, $pathParams, $campos, $responses, $headers, $validations, $requestBodyJson, $exampleRequest);

                $subsectionHeader = $m[1];
                $state = 'subsection';
                $inTable = false;
                $tableHeaderParsed = false;
                $tableRows = [];
                $capturedJson = '';
                $capturedCode = '';
                $captureJson = false;
                $captureCode = false;
                continue;
            }

            // Handle state-specific content
            if ($state === 'summary') {
                if ($trimmed !== '' && !str_starts_with($trimmed, '#') && !str_starts_with($trimmed, '---')) {
                    $summary .= ($summary ? ' ' : '') . $trimmed;
                }
                continue;
            }

            if ($state === 'subsection') {
                // Check for JSON code block start
                if (str_starts_with($trimmed, '```json')) {
                    $captureJson = true;
                    $capturedJson = '';
                    continue;
                }
                if ($captureJson) {
                    if (str_starts_with($trimmed, '```')) {
                        $captureJson = false;
                        continue;
                    }
                    $capturedJson .= $line . "\n";
                    continue;
                }

                // Check for plain code block start
                if (str_starts_with($trimmed, '```') && !str_starts_with($trimmed, '```json')) {
                    $captureCode = true;
                    $capturedCode = '';
                    continue;
                }
                if ($captureCode) {
                    if (str_starts_with($trimmed, '```')) {
                        $captureCode = false;
                        continue;
                    }
                    $capturedCode .= $line . "\n";
                    continue;
                }

                // Parse tables
                if (str_starts_with($trimmed, '|') && str_ends_with($trimmed, '|') && substr_count($trimmed, '|') >= 3) {
                    $cells = array_map('trim', explode('|', $trimmed));
                    $cells = array_values(array_filter($cells, fn($c) => $c !== ''));
                    $cellCount = count($cells);

                    // Skip separator rows (e.g., |---|---|)
                    if ($cellCount >= 2 && preg_match('/^[-:\s]+$/', $cells[0])) {
                        $tableHeaderParsed = true;
                        continue;
                    }

                    // Detect header row
                    $isHeader = false;
                    $headerKeywords = ['parámetro', 'parameter', 'campo', 'field', 'valor', 'value'];
                    foreach ($cells as $cell) {
                        foreach ($headerKeywords as $kw) {
                            if (mb_strtolower($cell) === $kw || str_contains(mb_strtolower($cell), $kw)) {
                                $isHeader = true;
                                break 2;
                            }
                        }
                    }

                    if ($isHeader && !$inTable) {
                        $inTable = true;
                        $tableHeaderParsed = false;
                        $tableRows = [];
                        continue;
                    }

                    if ($inTable && !$tableHeaderParsed) {
                        $tableHeaderParsed = true;
                        continue;
                    }

                    if ($inTable && $cellCount >= 2) {
                        $row = [];
                        if ($cellCount >= 1) $row['field'] = $cells[0];
                        if ($cellCount >= 2) $row['type'] = $cells[1];
                        if ($cellCount >= 3) $row['description'] = $cells[2];
                        if ($cellCount >= 4) $row['required'] = $cells[3];
                        $tableRows[] = $row;
                    }
                    continue;
                }

                // If table ended, capture non-table text
                if ($inTable && $trimmed !== '' && !str_starts_with($trimmed, '|')) {
                    // Table has ended, but we'll finalize on next subsection header
                }
            }
        }

        // Finalize last subsection
        $this->finalizeSubsection($subsectionHeader, $tableRows, $capturedJson, $capturedCode, $inTable,
            $queryParams, $pathParams, $campos, $responses, $headers, $validations, $requestBodyJson, $exampleRequest);

        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));

        return [
            'name' => $name,
            'slug' => $slug,
            'action_title' => $actionTitle ?: $name,
            'method' => $method,
            'path' => $path,
            'summary' => $summary,
            'permissions' => $permissions,
            'query_params' => $queryParams,
            'path_params' => $pathParams,
            'headers' => $headers,
            'request_body_json' => $requestBodyJson,
            'campos' => $campos,
            'validations' => $validations,
            'responses' => $responses,
            'example_request' => $exampleRequest,
        ];
    }

    protected function finalizeSubsection(string $header, array &$tableRows, string &$capturedJson, string &$capturedCode,
        bool &$inTable, array &$queryParams, array &$pathParams, array &$campos, array &$responses,
        string &$headers, string &$validations, string &$requestBodyJson, string &$exampleRequest): void
    {
        if (empty($header)) return;

        $headerLower = mb_strtolower($header);

        if (str_contains($headerLower, 'query parameter')) {
            $queryParams = $tableRows;
        } elseif (str_contains($headerLower, 'path parameter')) {
            $pathParams = $tableRows;
        } elseif (str_contains($headerLower, 'header')) {
            $headers = trim($capturedCode ?: $capturedJson);
        } elseif (str_contains($headerLower, 'request body')) {
            $requestBodyJson = trim($capturedJson);
        } elseif ($headerLower === 'campos' || str_contains($headerLower, 'campo')) {
            $campos = $tableRows;
        } elseif (str_contains($headerLower, 'validation')) {
            $validations = trim($capturedCode);
        } elseif (str_contains($headerLower, 'permission')) {
            // Permissions text captured in code block or as text
        } elseif (str_contains($headerLower, 'example request')) {
            $exampleRequest = trim($capturedCode ?: $capturedJson);
        } elseif (str_contains($headerLower, 'response')) {
            // Extract status code from header like "Response `200 OK`:"
            $statusCode = '200';
            $statusText = '';
            if (preg_match('/`(\d{3})\s+(.+?)`/', $header, $m)) {
                $statusCode = $m[1];
                $statusText = $m[2];
            }
            $responses[] = [
                'status_code' => $statusCode,
                'status_text' => $statusText,
                'json' => trim($capturedJson),
            ];
        }

        // Reset accumulators
        $tableRows = [];
        $capturedJson = '';
        $capturedCode = '';
        $inTable = false;
    }

    protected function parseFieldReferences(string $body): array
    {
        $sections = preg_split('/^### /m', $body);
        $references = [];

        foreach ($sections as $section) {
            if (empty(trim($section))) continue;

            $lines = explode("\n", $section);
            $title = trim(array_shift($lines));
            if (empty($title)) continue;

            $fields = [];
            $inTable = false;
            $headerParsed = false;

            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (str_starts_with($trimmed, '|') && str_ends_with($trimmed, '|') && substr_count($trimmed, '|') >= 3) {
                    $cells = array_map('trim', explode('|', $trimmed));
                    $cells = array_values(array_filter($cells, fn($c) => $c !== ''));
                    $cellCount = count($cells);

                    if ($cellCount >= 2 && preg_match('/^[-:\s]+$/', $cells[0])) {
                        $headerParsed = true;
                        continue;
                    }

                    $isHeader = false;
                    $headerKeywords = ['parámetro', 'parameter', 'campo', 'field', 'valor', 'value'];
                    foreach ($cells as $cell) {
                        foreach ($headerKeywords as $kw) {
                            if (mb_strtolower($cell) === $kw || str_contains(mb_strtolower($cell), $kw)) {
                                $isHeader = true;
                                break 2;
                            }
                        }
                    }

                    if ($isHeader && !$inTable) {
                        $inTable = true;
                        $headerParsed = false;
                        continue;
                    }

                    if ($inTable && !$headerParsed) {
                        $headerParsed = true;
                        continue;
                    }

                    if ($inTable && $cellCount >= 2) {
                        $row = ['name' => $cells[0]];
                        if ($cellCount >= 2) $row['type'] = $cells[1];
                        if ($cellCount >= 3) $row['description'] = $cells[2];
                        $fields[] = $row;
                    }
                } else {
                    if ($inTable && $trimmed !== '') {
                        $inTable = false;
                    }
                }
            }

            if (!empty($fields)) {
                $references[] = [
                    'title' => $title,
                    'fields' => $fields,
                ];
            }
        }

        return $references;
    }

    public function export()
    {
        $docsDir = base_path('docs/api');
        $modulesDir = $docsDir . '/modules';
        $lines = [];

        $lines[] = '# API Documentation — Export';
        $lines[] = '>';
        $lines[] = '> Generado el ' . now()->format('Y-m-d H:i:s');
        $lines[] = '>';
        $lines[] = '> Total de módulos: ' . count(glob($modulesDir . '/*.md'));
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '';

        foreach (['README.md', 'authentication.md', 'response-format.md'] as $baseFile) {
            $path = $docsDir . '/' . $baseFile;
            if (file_exists($path)) {
                $lines[] = file_get_contents($path);
                $lines[] = '';
                $lines[] = '---';
                $lines[] = '';
            }
        }

        $files = collect(scandir($modulesDir))
            ->filter(fn($f) => pathinfo($f, PATHINFO_EXTENSION) === 'md')
            ->sort()
            ->values();

        foreach ($files as $filename) {
            $lines[] = file_get_contents($modulesDir . '/' . $filename);
            $lines[] = '';
            $lines[] = '---';
            $lines[] = '';
        }

        $content = implode("\n", $lines);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'api-documentation-' . now()->format('Y-m-d') . '.md', [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }
}
