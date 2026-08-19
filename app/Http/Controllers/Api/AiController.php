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

    public function tools(): JsonResponse
    {
        return response()->json([
            'tools' => $this->toolsManager->getAvailableTools(),
        ]);
    }
}
