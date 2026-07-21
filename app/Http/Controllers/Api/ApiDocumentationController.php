<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\CommonMark\MarkdownConverter;

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

        return view('api.documentation', compact('modules'));
    }

    protected function parseModule(string $filename, string $content): ?array
    {
        $parts = explode('-', basename($filename, '.md'));
        $moduleName = ucwords(implode(' ', $parts));

        // Extract description (first paragraph after title)
        $descLines = preg_split('/\n/', $content);
        $description = '';
        $foundTitle = false;
        foreach ($descLines as $line) {
            if (trim($line) === '') continue;
            if (str_starts_with(trim($line), '# ')) {
                $foundTitle = true;
                continue;
            }
            if ($foundTitle && trim($line) !== '' && !str_starts_with(trim($line), '##')) {
                $description = trim($line);
                break;
            }
            if (!$foundTitle && !str_starts_with(trim($line), '#')) {
                $description = trim($line);
                break;
            }
        }

        // Split by ## headings to get endpoints
        $sections = preg_split('/(?=^## )/m', $content, -1, PREG_SPLIT_NO_EMPTY);

        $endpoints = [];
        $fieldReferences = [];
        $currentSection = null;

        foreach ($sections as $section) {
            $lines = explode("\n", $section);
            $firstLine = trim($lines[0]);

            if (str_starts_with($firstLine, '## Endpoint ')) {
                // This is an endpoint section
                $endpointName = trim(str_replace('## Endpoint ', '', $firstLine));
                $endpointSlug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $endpointName));

                $httpMethod = null;
                $httpPath = null;
                $summary = '';
                $headers = [];
                $queryParams = [];
                $requestBody = [];
                $pathParams = [];
                $responses = [];
                $requestExamples = [];

                $state = 'description'; // description, headers, params, body, response, example
                $inTable = false;
                $tableRows = [];
                $tableHeaderParsed = false;

                foreach (array_slice($lines, 1) as $line) {
                    $trimmed = trim($line);

                    if (str_starts_with($trimmed, '### ')) {
                        $httpMethod = strtoupper(explode(' ', trim($trimmed, '# '), 2)[1] ?? '');
                        $httpPath = trim(explode(' ', trim($trimmed, '# '), 2)[1] ?? '');
                        continue;
                    }

                    if (str_starts_with($trimmed, '## ')) {
                        break;
                    }

                    if ($trimmed === '**Headers:**') {
                        $state = 'headers';
                        continue;
                    }

                    if ($trimmed === '**Query Parameters:**' || $trimmed === '**Path Parameters:**') {
                        $state = 'params';
                        continue;
                    }

                    if ($trimmed === '**Request Body:**') {
                        $state = 'body';
                        continue;
                    }

                    if (str_starts_with($trimmed, '**Response ') || str_starts_with($trimmed, '**Response`')) {
                        $state = 'response';
                        continue;
                    }

                    if ($trimmed === '**Example Request:**') {
                        $state = 'example';
                        continue;
                    }

                    if (str_starts_with($trimmed, '|') && str_contains($trimmed, '|')) {
                        if (str_contains($trimmed, 'Parámetro') || str_contains($trimmed, 'Parameter') || str_contains($trimmed, 'Campo')) {
                            $inTable = true;
                            $tableHeaderParsed = false;
                            continue;
                        }
                        if ($inTable) {
                            $cells = array_map('trim', explode('|', $trimmed));
                            $cells = array_filter($cells, fn($c) => $c !== '');
                            $cells = array_values($cells);

                            if (!$tableHeaderParsed) {
                                $tableHeaderParsed = true;
                                continue;
                            }

                            if (count($cells) >= 2) {
                                $row = ['field' => $cells[0]];
                                for ($i = 1; $i < min(count($cells), 4); $i++) {
                                    $row['col' . $i] = $cells[$i];
                                }
                                $tableRows[] = $row;
                            }
                            continue;
                        }
                    }

                    if (str_starts_with($trimmed, '```json') || str_starts_with($trimmed, '```')) {
                        $state = 'raw_example';
                        continue;
                    }

                    if ($state === 'raw_example') {
                        if (str_starts_with($trimmed, '```')) {
                            $state = 'description';
                            continue;
                        }
                        if (!isset($currentSection)) $currentSection = '';
                        $currentSection .= $trimmed . "\n";
                        continue;
                    }

                    switch ($state) {
                        case 'description':
                            if ($trimmed !== '' && !str_starts_with($trimmed, '**')) {
                                $summary .= ($summary ? ' ' : '') . $trimmed;
                            }
                            break;
                        case 'headers':
                            if ($trimmed !== '' && !str_starts_with($trimmed, '`') && !str_starts_with($trimmed, '```')) {
                                // skip
                            }
                            break;
                        case 'params':
                            if ($inTable && !empty($tableRows)) {
                                if ($trimmed === '**Response**' || str_starts_with($trimmed, '**Response')) {
                                    $inTable = false;
                                    if ($httpMethod && $httpPath) {
                                        $endpoints[] = [
                                            'name' => $endpointName,
                                            'slug' => $endpointSlug,
                                            'method' => $httpMethod,
                                            'path' => $httpPath,
                                            'summary' => $summary,
                                            'query_params' => $tableRows,
                                        ];
                                    }
                                    $tableRows = [];
                                    $httpMethod = null;
                                    $httpPath = null;
                                    $summary = '';
                                    $queryParams = [];
                                    $requestBody = [];
                                    $pathParams = [];
                                    $responses = [];
                                }
                            }
                            break;
                        case 'body':
                            if ($inTable && !empty($tableRows)) {
                                if ($trimmed === '**Response**' || str_starts_with($trimmed, '**Response')) {
                                    $inTable = false;
                                    if ($httpMethod && $httpPath) {
                                        $endpoints[] = [
                                            'name' => $endpointName,
                                            'slug' => $endpointSlug,
                                            'method' => $httpMethod,
                                            'path' => $httpPath,
                                            'summary' => $summary,
                                            'request_body' => $tableRows,
                                        ];
                                    }
                                    $tableRows = [];
                                    $httpMethod = null;
                                    $httpPath = null;
                                    $summary = '';
                                    $queryParams = [];
                                    $requestBody = [];
                                    $pathParams = [];
                                    $responses = [];
                                }
                            }
                            break;
                        case 'response':
                            if (preg_match('/^\d+\s+[A-Z]/', $trimmed)) {
                                $responses[] = $trimmed;
                            }
                            break;
                    }
                }

                // Handle last table in params/body state
                if ($inTable && !empty($tableRows) && $httpMethod && $httpPath) {
                    $endpoints[] = [
                        'name' => $endpointName,
                        'slug' => $endpointSlug,
                        'method' => $httpMethod,
                        'path' => $httpPath,
                        'summary' => $summary,
                        'query_params' => $tableRows,
                    ];
                }

            } elseif (str_starts_with($firstLine, '## Field Reference')) {
                // Parse field references
                $fieldRefSections = preg_split('/(?=^### )/m', $section, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($fieldRefSections as $refSection) {
                    $refLines = explode("\n", $refSection);
                    $refTitle = trim(str_replace('### ', '', $refLines[0]));
                    $fields = [];
                    $inFieldTable = false;
                    $headerParsed = false;

                    foreach (array_slice($refLines, 1) as $line) {
                        $t = trim($line);
                        if (str_starts_with($t, '|') && str_contains($t, '|')) {
                            if (str_contains($t, 'Campo') || str_contains($t, 'Field') || str_contains($t, 'Parameter')) {
                                $inFieldTable = true;
                                $headerParsed = false;
                                continue;
                            }
                            if ($inFieldTable) {
                                $cells = array_map('trim', explode('|', $t));
                                $cells = array_filter($cells, fn($c) => $c !== '');
                                $cells = array_values($cells);
                                if (!$headerParsed) {
                                    $headerParsed = true;
                                    continue;
                                }
                                if (count($cells) >= 3) {
                                    $fields[] = [
                                        'name' => $cells[0],
                                        'type' => $cells[1],
                                        'description' => $cells[2] ?? '',
                                    ];
                                }
                            }
                        }
                    }

                    if (!empty($fields)) {
                        $fieldReferences[] = [
                            'title' => $refTitle,
                            'fields' => $fields,
                        ];
                    }
                }
            }
        }

        if (empty($endpoints)) {
            // Fallback: treat entire file as a single endpoint
            $endpoints[] = [
                'name' => $moduleName,
                'slug' => 'overview',
                'method' => 'REST',
                'path' => '/api/' . basename($filename, '.md'),
                'summary' => $description,
                'query_params' => [],
            ];
        }

        return [
            'filename' => $filename,
            'name' => $moduleName,
            'description' => $description,
            'endpoints' => $endpoints,
            'field_references' => $fieldReferences,
            'raw_content' => $content,
        ];
    }
}
