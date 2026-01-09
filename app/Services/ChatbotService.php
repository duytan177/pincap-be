<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('ai_services.base_url') . config('ai_services.endpoint.chatbot');
    }

    /**
     * Call FastAPI chatbot endpoint
     *
     * @param string $userMessage User's message
     * @param string $userId User ID
     * @param array|null $conversationHistory Conversation history
     * @param array|null $suggestedMediaIds Suggested media IDs
     * @param string|null $fileUrl File URL
     * @param string|null $token JWT token
     * @return array Chatbot response
     */
    public function processMessage(
        string $userMessage,
        string $userId,
        ?array $conversationHistory = null,
        ?array $suggestedMediaIds = null,
        ?string $fileUrl = null,
        ?string $token = null
    ): array {
        try {
            $payload = [
                'user_id' => $userId,
                'message' => $userMessage,
            ];

            if ($conversationHistory !== null) {
                $payload['conversation_history'] = $conversationHistory;
            }

            if ($suggestedMediaIds !== null) {
                $payload['suggested_media_ids'] = $suggestedMediaIds;
            }

            if ($fileUrl !== null) {
                $payload['file_url'] = $fileUrl;
            }

            if ($token !== null) {
                $payload['token'] = $token;
            }

            Log::info('Chatbot API Request', [
                'url' => $this->baseUrl,
                'payload' => $payload
            ]);

            $response = Http::timeout(120)
                ->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error('Chatbot API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'error' => true,
                    'message' => 'Chatbot service error',
                    'detail' => $response->body()
                ];
            }

            $responseData = $response->json();

            Log::info('Chatbot API Response', [
                'response' => $responseData
            ]);

            return [
                'error' => false,
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Chatbot Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => true,
                'message' => 'Exception calling chatbot service',
                'detail' => $e->getMessage()
            ];
        }
    }
}

