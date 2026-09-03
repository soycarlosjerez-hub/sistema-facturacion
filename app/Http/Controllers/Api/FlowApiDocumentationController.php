<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FlowApiDocumentationController extends Controller
{
    public function index()
    {
        $docsDir = base_path('docs/api/modules');
        $modules = [];

        $targetFile = $docsDir . DIRECTORY_SEPARATOR . 'flow-api.md';
        if (file_exists($targetFile)) {
            $content = file_get_contents($targetFile);
            $module = $this->parseModule('flow-api.md', $content);
            if ($module) {
                $modules[] = $module;
            }
        }

        return view('api.flowapi', compact('modules'));
    }

    protected function parseModule(string $filename, string $content): ?array
    {
        $moduleName = 'FlowApi';

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
        $queryParams = [];
        $pathParams = [];
        $campos = [];
        $responses = [];
        $requestBodyJson = '';
        $headers = '';
        $validations = '';
        $exampleRequest = '';

        $lines = explode("\n", $body);
        $i = 0;
        $total = count($lines);

        if ($i < $total && str_starts_with(trim($lines[$i]), '### ')) {
            $actionTitle = trim(str_replace('### ', '', trim($lines[$i])));
            $i++;
        }

        for ($j = $i; $j < $total; $j++) {
            $line = $lines[$j];
            if (preg_match('/\*\*`(GET|POST|PUT|PATCH|DELETE)\s+(\/.*?)`\*\*/', $line, $m)) {
                $method = $m[1];
                $path = $m[2];
                $i = $j + 1;
                break;
            }
            if (preg_match('/\*\*(GET|POST|PUT|PATCH|DELETE)\s+(\/.*?)\*\*/', $line, $m)) {
                $method = $m[1];
                $path = $m[2];
                $i = $j + 1;
                break;
            }
        }

        $state = 'summary';
        $subsectionHeader = '';
        $captureJson = false;
        $captureCode = false;
        $capturedJson = '';
        $capturedCode = '';
        $inTable = false;
        $tableHeaderParsed = false;
        $tableRows = [];

        for (; $i < $total; $i++) {
            $line = $lines[$i];
            $trimmed = trim($line);

            if (preg_match('/^\*\*(.+?):\*\*\s*$/', $trimmed, $m)) {
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

            if ($state === 'summary') {
                if ($trimmed !== '' && !str_starts_with($trimmed, '#') && !str_starts_with($trimmed, '---')) {
                    $summary .= ($summary ? ' ' : '') . $trimmed;
                }
                continue;
            }

            if ($state === 'subsection') {
                if (str_starts_with($trimmed, '```json')) {
                    $captureJson = true;
                    $capturedJson = '';
                    continue;
                }
                if ($captureJson) {
                    if (str_starts_with($trimmed, '```')) { $captureJson = false; continue; }
                    $capturedJson .= $line . "\n";
                    continue;
                }

                if (str_starts_with($trimmed, '```') && !str_starts_with($trimmed, '```json')) {
                    $captureCode = true;
                    $capturedCode = '';
                    continue;
                }
                if ($captureCode) {
                    if (str_starts_with($trimmed, '```')) { $captureCode = false; continue; }
                    $capturedCode .= $line . "\n";
                    continue;
                }

                if (str_starts_with($trimmed, '|') && str_ends_with($trimmed, '|') && substr_count($trimmed, '|') >= 3) {
                    $cells = array_map('trim', explode('|', $trimmed));
                    $cells = array_values(array_filter($cells, fn($c) => $c !== ''));
                    $cellCount = count($cells);

                    if ($cellCount >= 2 && preg_match('/^[-:\s]+$/', $cells[0])) {
                        $tableHeaderParsed = true;
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
            }
        }

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
            'permissions' => '',
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
        } elseif (str_contains($headerLower, 'response')) {
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
                    foreach ($cells as $cell) {
                        if (in_array(mb_strtolower($cell), ['campo', 'field', 'parámetro', 'parameter', 'valor', 'value'])) {
                            $isHeader = true;
                            break;
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
        $targetFile = base_path('docs/api/modules/flow-api.md');
        $content = file_exists($targetFile) ? file_get_contents($targetFile) : '';

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'flowapi-documentation-' . now()->format('Y-m-d') . '.md', [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }
}
