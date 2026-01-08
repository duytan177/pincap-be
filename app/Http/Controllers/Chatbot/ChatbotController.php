<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chatbot\ChatbotRequest;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Main chatbot endpoint for media management queries.
     *
     * Handles:
     * - SEARCH_MEDIA: Search and answer questions about media
     * - SUGGEST_MEDIA: Suggest media and ask for album confirmation
     * - CONFIRM_CREATE_ALBUM: Create album from suggested media
     * - CREATE_MEDIA_FROM_INPUT: Generate metadata for new media
     * - GENERAL_QA: General questions
     *
     * @param ChatbotRequest $request
     * @return JsonResponse
     */
    public function __invoke(ChatbotRequest $request): JsonResponse
    {
        $userId = JWTAuth::user()->getAttribute("id");
        
        $result = $this->chatbotService->processMessage(
            userMessage: $request->input('message'),
            userId: $userId,
            conversationHistory: $request->input('conversation_history'),
            suggestedMediaIds: $request->input('suggested_media_ids'),
            fileUrl: $request->input('file_url')
        );

        if ($result['error']) {
            return response()->json([
                'error' => true,
                'message' => $result['message'] ?? 'Chatbot service error',
                'detail' => $result['detail'] ?? null
            ], 500);
        }

        return response()->json($result['data'], 200);
    }
}

