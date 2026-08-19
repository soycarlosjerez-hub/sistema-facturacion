<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AiChatRequest;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\Ai\AiService;
use App\Services\Ai\Tools\AiToolsManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiController extends Controller
{
    public function __construct(
        protected AiService $aiService,
        protected AiToolsManager $toolsManager
    ) {}

    public function chat(AiChatRequest $request): StreamedResponse|JsonResponse
    {
        $user = auth()->user();

        if (config('ai.stream', true) && $request->wantsStreaming()) {
            return $this->aiService->streamChat(
                $request->input('message'),
                $request->input('conversation_id'),
                $user
            );
        }

        try {
            $result = $this->aiService->chat(
                $request->input('message'),
                $request->input('conversation_id'),
                $user
            );

            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno del servidor.'], 500);
        }
    }

    public function conversations(): JsonResponse
    {
        $conversations = AiConversation::where('user_id', auth()->id())
            ->orderByDesc('updated_at')
            ->take(50)
            ->get(['id', 'conversation_id', 'title', 'created_at', 'updated_at']);

        return response()->json($conversations);
    }

    public function showConversation(string $conversation): JsonResponse
    {
        $messages = AiMessage::where('conversation_id', $conversation)
            ->whereHas('conversation', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderBy('created_at')
            ->get(['id', 'conversation_id', 'role', 'content', 'tool_name', 'tool_arguments', 'tool_result', 'created_at']);

        return response()->json($messages);
    }

    public function debug(): \Illuminate\Http\JsonResponse
    {
        $errors = [];
        
        // Check all tool classes exist
        foreach (config('ai.tools', []) as $name => $class) {
            if (!class_exists($class)) {
                $errors[] = "Tool class '{$class}' does NOT exist";
            }
        }
        
        // Check imports in files
        $toolsDir = base_path('app/Services/Ai/Tools');
        foreach (glob("{$toolsDir}/*.php") as $file) {
            $content = file_get_contents($file);
            if (strpos($content, 'Auth::') !== false && strpos($content, 'use Illuminate\\Support\\Facades\\Auth') === false) {
                $errors[] = basename($file) . " uses Auth:: without import";
            }
        }
        
        return response()->json([
            'base_path' => base_path(),
            'ai_config_loaded' => config('ai.api_url') !== null,
            'tools_count' => count(config('ai.tools', [])),
            'auth_check' => auth()->check(),
            'user_data' => auth()->check() ? [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'business_instance_id' => auth()->user()->business_instance_id,
            ] : null,
            'errors' => $errors,
            'php_version' => PHP_VERSION,
            'db_connection' => config('database.connections.mysql.database'),
        ]);
    }

    public function tools(): JsonResponse
    {
        return response()->json([
            'tools' => $this->toolsManager->getAvailableTools(),
        ]);
    }
}
