<?php

namespace App\Services\Ai;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\Ai\Tools\AiToolsManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiService
{
    public function __construct(
        protected AiToolsManager $toolsManager
    ) {}

    public function chat(string $message, ?string $conversationId, Authenticatable $user): array
    {
        $tenantId = $user->business_instance_id;
        $userId = $user->id;

        DB::beginTransaction();

        try {
            if (!$conversationId) {
                $conversation = $this->createConversation($tenantId, $userId);
                $conversationId = $conversation->conversation_id;
            } else {
                $conversation = $this->getConversation($conversationId, $userId);
                if (!$conversation) {
                    throw new \RuntimeException('Conversacional no encontrada o no pertenece al usuario actual.');
                }
            }

            $this->saveMessageInternal($conversationId, 'user', $message);

            $contextMessages = $this->getContextMessagesInternal($conversationId);
            $systemPrompt = $this->buildSystemPrompt($user);

            $messages = collect([$systemPrompt])->merge($contextMessages)->values()->toArray();

            $tools = $this->toolsManager->getAvailableTools();

            $response = $this->callLLM($messages, $tools);

            $assistantContent = '';
            $pendingToolCalls = [];
            $toolResponses = [];
            $hasReasoning = false;

            if (isset($response['choices'])) {
                foreach ($response['choices'] as $choice) {
                    if (isset($choice['delta']['content']) && $choice['delta']['content']) {
                        $assistantContent .= $choice['delta']['content'];
                    }

                    if (isset($choice['message']['content'])) {
                        $assistantContent .= $choice['message']['content'];
                    }

                    if (!empty($choice['message']['reasoning'])) {
                        $hasReasoning = true;
                    }

                    if (isset($choice['delta']['tool_calls'])) {
                        foreach ($choice['delta']['tool_calls'] as $tc) {
                            if (!isset($pendingToolCalls[$tc['index']])) {
                                $pendingToolCalls[$tc['index']] = [
                                    'id' => $tc['id'] ?? '',
                                    'function_name' => '',
                                    'arguments' => '',
                                ];
                            }

                            if (isset($tc['function']['name'])) {
                                $pendingToolCalls[$tc['index']]['function_name'] = $tc['function']['name'];
                            }

                            if (isset($tc['function']['arguments'])) {
                                $pendingToolCalls[$tc['index']]['arguments'] .= $tc['function']['arguments'];
                            }
                        }
                    }

                    if (isset($choice['message']['tool_calls'])) {
                        foreach ($choice['message']['tool_calls'] as $tc) {
                            $index = $tc['index'] ?? count($pendingToolCalls);

                            if (!isset($pendingToolCalls[$index])) {
                                $pendingToolCalls[$index] = [
                                    'id' => $tc['id'] ?? '',
                                    'function_name' => '',
                                    'arguments' => '',
                                ];
                            }

                            if (isset($tc['function']['name'])) {
                                $pendingToolCalls[$index]['function_name'] = $tc['function']['name'];
                            }

                            if (isset($tc['function']['arguments'])) {
                                $pendingToolCalls[$index]['arguments'] .= $tc['function']['arguments'];
                            }
                        }
                    }
                }
            }

            if (count($pendingToolCalls) > 0) {
                $toolResponses = [];

                foreach ($pendingToolCalls as $tc) {
                    $toolName = $tc['function_name'];
                    $arguments = json_decode($tc['arguments'], true) ?: [];

                    $this->saveMessageInternal($conversationId, 'tool_call', $toolName, json_encode($arguments));

                    try {
                        $result = $this->toolsManager->executeTool($toolName, $arguments, $user);
                        $toolResponses[] = [
                            'tool_call_id' => $tc['id'],
                            'tool_name' => $toolName,
                            'tool_result' => $result,
                        ];
                    } catch (\Exception $e) {
                        $toolResponses[] = [
                            'tool_call_id' => $tc['id'],
                            'tool_name' => $toolName,
                            'tool_result' => ['error' => $e->getMessage()],
                        ];
                    }
                }

                $toolMessages = [];
                foreach ($toolResponses as $tr) {
                    $toolMessages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $tr['tool_call_id'],
                        'content' => json_encode($tr['tool_result']),
                    ];
                }

                $messages = collect($messages)->merge($toolMessages)->values()->toArray();

                $response = $this->callLLM($messages, $tools);

                if (isset($response['choices'])) {
                    foreach ($response['choices'] as $choice) {
                        $assistantContent .= $choice['message']['content'] ?? '';

                        if (!empty($choice['message']['reasoning'])) {
                            $hasReasoning = true;
                        }
                    }
                }
            }

            $this->saveMessageInternal($conversationId, 'assistant', trim($assistantContent));

            if (empty($assistantContent)) {
                if ($hasReasoning) {
                    $assistantContent = 'El modelo agoto el limite de tokens durante el razonamiento y no genero respuesta. Intenta con una pregunta mas especifica.';
                } else {
                    $assistantContent = 'No pude obtener una respuesta. Por favor, intenta de nuevo con una pregunta diferente.';
                }
                $this->saveMessageInternal($conversationId, 'assistant', $assistantContent);
            }

            $conversation->touch();

            DB::commit();

            return [
                'conversation_id' => $conversationId,
                'message' => $assistantContent,
                'tools_used' => array_unique(array_map(fn($tr) => $tr['tool_name'], $toolResponses)),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function streamChat(string $message, ?string $conversationId, Authenticatable $user): StreamedResponse
    {
        try {
            if (!$conversationId) {
                $conversation = $this->createConversation($user->business_instance_id, $user->id);
                $conversationId = $conversation->conversation_id;
            } else {
                $conversation = $this->getConversation($conversationId, $user->id);
                if (!$conversation) {
                    throw new \RuntimeException('Conversacion no encontrada.');
                }
            }

            $this->saveMessageInternal($conversationId, 'user', $message);

            $contextMessages = $this->getContextMessagesInternal($conversationId);
            $systemPrompt = $this->buildSystemPrompt($user);

            $promptMessages = collect([$systemPrompt])->merge($contextMessages)->values()->toArray();

            $tools = $this->toolsManager->getAvailableTools();

            return new StreamedResponse(function () use ($user, $conversationId, $systemPrompt, $promptMessages, $tools) {
                $buffer = '';
                $pendingToolCalls = [];
                $fullAssistantContent = '';
                $reasoningBuffer = '';
                $lastSentReasoning = 0;
                $finishReason = null;

                try {
                    echo "data: " . json_encode(['type' => 'conversation_start', 'conversation_id' => $conversationId]) . "\n\n";
                    flush();

                    $ch = $this->buildCurlHandle($promptMessages, $tools, true);

                    curl_setopt_array($ch, [
                        CURLOPT_WRITEFUNCTION => function ($curlHandle, $data) use (&$buffer, &$pendingToolCalls, &$fullAssistantContent, &$reasoningBuffer, &$lastSentReasoning, &$finishReason) {
                            $buffer .= $data;
                            $lines = explode("\n", $buffer);

                            if (count($lines) <= 1) {
                                return strlen($data);
                            }

                            $buffer = array_pop($lines);

                            foreach ($lines as $line) {
                                if (str_starts_with($line, 'data: ')) {
                                    $jsonStr = trim(substr($line, 6));
                                    if ($jsonStr === '[DONE]') {
                                        continue;
                                    }

                                    $decoded = json_decode($jsonStr, true);
                                    if (!$decoded || !isset($decoded['choices'])) {
                                        continue;
                                    }

                                    foreach ($decoded['choices'] as $choice) {
                                        $delta = $choice['delta'] ?? [];
                                        $finishReason = $choice['finish_reason'] ?? $finishReason;

                                        if (isset($delta['reasoning']) && $delta['reasoning']) {
                                            $reasoningBuffer .= $delta['reasoning'];
                                            if (strlen($reasoningBuffer) - $lastSentReasoning >= 100) {
                                                echo "data: " . json_encode(['type' => 'thinking']) . "\n\n";
                                                flush();
                                                $lastSentReasoning = strlen($reasoningBuffer);
                                            }
                                        }

                                        if (isset($delta['content']) && $delta['content']) {
                                            if ($reasoningBuffer !== '' && $fullAssistantContent === '') {
                                                echo "data: " . json_encode(['type' => 'thinking_done']) . "\n\n";
                                                flush();
                                            }
                                            $fullAssistantContent .= $delta['content'];
                                            echo "data: " . json_encode(['type' => 'text', 'content' => $delta['content']]) . "\n\n";
                                            flush();
                                        }

                                        if (isset($delta['tool_calls'])) {
                                            foreach ($delta['tool_calls'] as $tc) {
                                                if (!isset($pendingToolCalls[$tc['index']])) {
                                                    $pendingToolCalls[$tc['index']] = [
                                                        'id' => $tc['id'] ?? '',
                                                        'function_name' => '',
                                                        'arguments' => '',
                                                    ];
                                                }

                                                if (isset($tc['function']['name'])) {
                                                    $pendingToolCalls[$tc['index']]['function_name'] = $tc['function']['name'];
                                                }

                                                if (isset($tc['function']['arguments'])) {
                                                    $pendingToolCalls[$tc['index']]['arguments'] .= $tc['function']['arguments'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            return strlen($data);
                        },
                    ]);

                    curl_exec($ch);
                    $curlError = curl_error($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
                    curl_close($ch);

                    if ($httpCode >= 400) {
                        Log::error('AI stream HTTP error', [
                            'conversation_id' => $conversationId,
                            'http_code' => $httpCode,
                        ]);

                        echo "data: " . json_encode(['type' => 'error', 'message' => 'Error al comunicarse con el servicio de IA. HTTP: ' . $httpCode]) . "\n\n";
                        flush();

                        $this->saveMessageInternal($conversationId, 'assistant', 'Error al comunicarse con el servicio de IA. HTTP: ' . $httpCode);
                        return;
                    }

                    if ($curlError) {
                        Log::error('AI stream curl error', [
                            'conversation_id' => $conversationId,
                            'error' => $curlError,
                        ]);

                        echo "data: " . json_encode(['type' => 'error', 'message' => 'Error de conexion con el servicio de IA.']) . "\n\n";
                        flush();

                        $this->saveMessageInternal($conversationId, 'assistant', 'Error de conexion con el servicio de IA.');
                        return;
                    }

                    if ($finishReason === 'length' && trim($fullAssistantContent) === '' && count($pendingToolCalls) === 0) {
                        Log::warning('AI stream truncated by token limit', [
                            'conversation_id' => $conversationId,
                            'reasoning_chars' => strlen($reasoningBuffer),
                        ]);

                        echo "data: " . json_encode(['type' => 'error', 'message' => 'El modelo agoto el limite de tokens durante el razonamiento y no genero respuesta. Intenta con una pregunta mas especifica.']) . "\n\n";
                        flush();

                        $this->saveMessageInternal($conversationId, 'assistant', 'El modelo agoto el limite de tokens durante el razonamiento y no genero respuesta.');
                        return;
                    }

                    $toolResponses = [];

                    foreach ($pendingToolCalls as $tc) {
                        $toolName = $tc['function_name'];
                        $arguments = json_decode($tc['arguments'], true) ?: [];

                        $this->saveMessageInternal($conversationId, 'tool_call', $toolName, json_encode($arguments));

                        try {
                            $result = app(AiToolsManager::class)->executeTool($toolName, $arguments, $user);
                            $toolResponses[] = [
                                'tool_call_id' => $tc['id'],
                                'tool_name' => $toolName,
                                'tool_result' => $result,
                            ];
                            echo "data: " . json_encode(['type' => 'tool_used', 'tool_name' => $toolName]) . "\n\n";
                            flush();
                        } catch (\Exception $e) {
                            $toolResponses[] = [
                                'tool_call_id' => $tc['id'],
                                'tool_name' => $toolName,
                                'tool_result' => ['error' => $e->getMessage()],
                            ];
                            echo "data: " . json_encode(['type' => 'tool_error', 'tool_name' => $toolName, 'message' => $e->getMessage()]) . "\n\n";
                            flush();
                        }
                    }

                    if (count($toolResponses) > 0) {
                        $toolMessages = [];
                        foreach ($toolResponses as $tr) {
                            $toolMessages[] = [
                                'role' => 'tool',
                                'tool_call_id' => $tr['tool_call_id'],
                                'content' => json_encode($tr['tool_result']),
                            ];
                        }

                        $promptMessages = collect($promptMessages)->merge($toolMessages)->values()->toArray();

                        echo "data: " . json_encode(['type' => 'tool_response_start']) . "\n\n";
                        flush();

                        $finalCh = $this->buildCurlHandle($promptMessages, [], false);
                        curl_setopt($finalCh, CURLOPT_RETURNTRANSFER, true);

                        $finalBody = curl_exec($finalCh);
                        $finalCurlError = curl_error($finalCh);
                        $finalHttpCode = curl_getinfo($finalCh, CURLINFO_RESPONSE_CODE);
                        curl_close($finalCh);

                        if ($finalHttpCode >= 400 || $finalCurlError || !$finalBody) {
                            Log::error('AI stream final response failed', [
                                'conversation_id' => $conversationId,
                                'http_code' => $finalHttpCode,
                                'curl_error' => $finalCurlError,
                            ]);

                            echo "data: " . json_encode(['type' => 'error', 'message' => 'Error al procesar la respuesta final de la herramienta.']) . "\n\n";
                            flush();
                        } else {
                            $finalDecoded = json_decode($finalBody, true);
                            $finalContent = $finalDecoded['choices'][0]['message']['content'] ?? '';
                            $finalFinish = $finalDecoded['choices'][0]['finish_reason'] ?? null;

                            if ($finalContent === '' && $finalFinish === 'length') {
                                Log::warning('AI stream final response truncated', [
                                    'conversation_id' => $conversationId,
                                ]);

                                echo "data: " . json_encode(['type' => 'error', 'message' => 'El modelo agoto el limite de tokens durante el razonamiento y no genero respuesta.']) . "\n\n";
                                flush();
                            }

                            if ($finalContent !== '') {
                                foreach (str_split($finalContent, 80) as $chunk) {
                                    $fullAssistantContent .= $chunk;
                                    echo "data: " . json_encode(['type' => 'text', 'content' => $chunk]) . "\n\n";
                                    flush();
                                }
                            }
                        }

                        echo "data: " . json_encode(['type' => 'done']) . "\n\n";
                        flush();
                    } else {
                        echo "data: " . json_encode(['type' => 'done']) . "\n\n";
                        flush();
                    }

                    $savedContent = trim($fullAssistantContent);
                    if ($savedContent === '') {
                        $savedContent = 'No pude obtener una respuesta. Por favor, intenta de nuevo.';
                    }
                    $this->saveMessageInternal($conversationId, 'assistant', $savedContent);
                    AiConversation::where('conversation_id', $conversationId)->update(['updated_at' => now()]);

                } catch (\Exception $e) {
                    Log::error('AI stream failed', [
                        'conversation_id' => $conversationId,
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    echo "data: " . json_encode(['type' => 'error', 'message' => $e->getMessage()]) . "\n\n";
                    flush();

                    $this->saveMessageInternal($conversationId, 'assistant', 'Ocurrio un error al procesar tu mensaje. Por favor, intenta de nuevo.');
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ]);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function createConversation(int $tenantId, int $userId): AiConversation
    {
        return AiConversation::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'conversation_id' => (string) Str::uuid(),
        ]);
    }

    private function getConversation(string $conversationId, ?int $userId = null): ?AiConversation
    {
        $query = AiConversation::where('conversation_id', $conversationId);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->first();
    }

    private function saveMessageInternal(string $conversationId, string $role, string $content, ?string $toolName = null, ?string $toolArguments = null, ?string $toolResult = null): void
    {
        AiMessage::create([
            'conversation_id' => $conversationId,
            'role' => $role,
            'content' => $content,
            'tool_name' => $toolName,
            'tool_arguments' => $toolArguments,
            'tool_result' => $toolResult,
        ]);
    }

    private function getContextMessagesInternal(string $conversationId): array
    {
        return AiMessage::where('conversation_id', $conversationId)
            ->orderBy('id', 'desc')
            ->limit(config('ai.max_context_messages', 20))
            ->get()
            ->reverse()
            ->map(function ($msg) {
                if ($msg->role === 'tool_call') {
                    return [
                        'role' => 'assistant',
                        'content' => null,
                        'tool_calls' => [[
                            'id' => (string) $msg->id,
                            'type' => 'function',
                            'function' => [
                                'name' => $msg->tool_name,
                                'arguments' => $msg->tool_arguments ?? '{}',
                            ],
                        ]],
                    ];
                }

                if ($msg->role === 'tool_response') {
                    return [
                        'role' => 'tool',
                        'tool_call_id' => (string) $msg->tool_name,
                        'content' => $msg->content,
                    ];
                }

                return [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ];
            })
            ->values()
            ->toArray();
    }

    private function buildSystemPrompt(Authenticatable $user): array
    {
        $businessName = '';
        if ($user->businessInstance) {
            $businessName = $user->businessInstance->nombre ?? 'Tu Empresa';
        }

        $roleName = 'Sin rol';
        if ($user->roles && $user->roles->first()) {
            $roleName = $user->roles->first()->name;
        }

        $systemPrompt = str_replace(
            ['{business_name}', '{user_name}', '{role}'],
            [$businessName, $user->name ?? 'Usuario', $roleName],
            config('ai.system_prompt', 'Eres un asistente de solo lectura del sistema ERP.')
        );

        return [
            'role' => 'system',
            'content' => $systemPrompt,
        ];
    }

    private function callLLM(array $messages, array $tools): array
    {
        $url = $this->resolveApiUrl();
        if (!$url) {
            throw new \RuntimeException('AI_API_URL no esta configurada en .env');
        }

        $apiKey = config('ai.api_key');
        if (!$apiKey) {
            throw new \RuntimeException('AI_API_KEY no esta configurada en .env');
        }

        $model = config('ai.model', 'gpt-4o');
        $timeout = config('ai.timeout', 60);

        $ch = curl_init();

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => config('ai.temperature', 0.3),
            'max_tokens' => config('ai.max_tokens', 2000),
            'stream' => false,
        ];

        if (config('ai.disable_thinking')) {
            $payload['chat_template_kwargs'] = ['enable_thinking' => false];
        }

        if (count($tools) > 0) {
            $payload['tools'] = $tools;
        }

        curl_setopt_array($ch, $this->sslOptions() + [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $body = is_string($response) ? substr($response, 0, 500) : '';

            throw new \RuntimeException(
                sprintf(
                    'Error al comunicarse con el servicio de IA. HTTP: %d. URL: %s. Respuesta: %s',
                    $httpCode,
                    $url,
                    $body
                )
            );
        }

        return json_decode($response, true);
    }

    private function buildCurlHandle(array $messages, array $tools, bool $stream): \CurlHandle
    {
        $ch = curl_init();

        $payload = [
            'model' => config('ai.model'),
            'messages' => $messages,
            'temperature' => config('ai.temperature', 0.3),
            'max_tokens' => config('ai.max_tokens', 2000),
            'stream' => $stream,
        ];

        if (config('ai.disable_thinking')) {
            $payload['chat_template_kwargs'] = ['enable_thinking' => false];
        }

        if (count($tools) > 0) {
            $payload['tools'] = $tools;
        }

        curl_setopt_array($ch, $this->sslOptions() + [
            CURLOPT_URL => $this->resolveApiUrl(),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_TIMEOUT => config('ai.timeout', 60),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . config('ai.api_key'),
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        return $ch;
    }

    private function sslOptions(): array
    {
        $options = [];

        if (config('ai.ssl_verify') === false) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
        } else {
            $caBundle = config('ai.ca_bundle');
            if ($caBundle) {
                $options[CURLOPT_CAINFO] = $caBundle;
            }
        }

        return $options;
    }

    private function resolveApiUrl(): string
    {
        $url = (string) config('ai.api_url');

        if ($url !== '' && !str_ends_with(rtrim($url, '/'), '/chat/completions')) {
            $url = rtrim($url, '/') . '/chat/completions';
        }

        return $url;
    }
}
