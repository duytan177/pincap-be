<?php

namespace App\Services;

use App\Models\Prompt;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageAIService
{
    private ?string $systemPrompt;
    private string $userPrompt;
    private string $baseUrl;

    /**
     * Constructor
     * - Initializes system & user prompts from database
     * - Sets the target AI service endpoint
     */
    public function __construct(?string $keyPrompt = null, ?string $endpointKey = null)
    {
        $this->baseUrl = config('ai_services.base_url') . config("ai_services.endpoint.{$endpointKey}");

        // Retrieve system & user prompts from database
        $record = Prompt::where('key', $keyPrompt)->firstOrFail(['system_prompt', 'user_prompt']);
        $this->systemPrompt = $record?->system_prompt ?? null;
        $this->userPrompt = $record?->user_prompt;
    }

    /**
     * Main call method
     * - If there are image_url or files, send as multipart/form-data
     * - Otherwise, send as JSON (text-to-text)
     */
    public function call(array $data)
    {
        if (empty($this->userPrompt)) {
            throw new \RuntimeException('User prompt not initialized.');
        }


        // Replace placeholders in user prompt (e.g. {name})
        $this->userPrompt = $this->replacePlaceholders($this->userPrompt, $data);
        Log::info('AI Service call', [
            'url' => $this->baseUrl,
            'user_prompt' => $this->userPrompt,
            "data" => $data
        ]);
        // Determine request type
        if (array_key_exists('image_url', $data) || array_key_exists('files', $data)) {
            return $this->callAsFormData($data);
        }

        return $this->callAsJson($data);
    }

    /**
     * Send request as JSON
     * Used for text-to-text requests
     */
    protected function callAsJson(array $data)
    {
        $response = Http::timeout(60)->post($this->baseUrl, [
            'system_prompt' => $this->systemPrompt ?? null,
            'user_prompt' => $this->userPrompt,
            'data' => $data,
        ]);


        if (!$response->successful()) {
            throw new \Exception("AI Service Error ({$response->status()}): " . $response->body());
        }
        return $response->json('data');
    }

    /**
     * Send request as multipart/form-data
     * Used for text-to-image or image-based tasks
     */
    protected function callAsFormData(array $data)
    {
        $request = Http::timeout(90)->asMultipart();

        // Attach text fields
        $request = $request
            ->attach('user_prompt', $this->userPrompt);
        if ($this->systemPrompt) {
            $request = $request->attach('system_prompt', $this->systemPrompt);
        }
        // Attach single image from URL
        if (!empty($data['image_url'])) {
            $imageContent = Http::get($data['image_url'])->body();

            // Lấy tên file từ URL
            $parsedUrl = parse_url($data['image_url'], PHP_URL_PATH);
            $fileName = basename($parsedUrl); // ví dụ: "abc.png"

            $request = $request->attach('files', $imageContent, $fileName);
        }

        // Attach uploaded files (1 or 2 images)
        if (!empty($data['files'])) {
            foreach ($data['files'] as $i => $file) {
                $request = $request->attach(
                    "files[$i]",
                    $file->get(),
                    $file->getClientOriginalName()
                );
            }
        }

        $response = $request->post($this->baseUrl);
        if (!$response->successful()) {
            throw new \Exception("AI Service Error ({$response->status()}): " . $response->body());
        }

        return $response->json('data');
    }

    /**
     * Replace placeholders like {key} in the prompt template with data values
     */
    private function replacePlaceholders(string $template, array $data): string
    {
        return preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($data) {
            $key = $matches[1];
            return Arr::get($data, $key, '');
        }, $template);
    }
}
