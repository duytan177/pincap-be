<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class GoogleVisionService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected bool $enable;

    /**
     * Load config khi khởi tạo
     */
    public function __construct()
    {
        $this->apiKey = config('services.google_vision.api_key');
        $this->apiUrl = config('services.google_vision.url');
        $this->enable = config('services.google_vision.enable', false);
    }

    /**
     * Gọi Google Vision API - SAFE_SEARCH_DETECTION cho nhiều ảnh
     *
     * @param array $base64Images
     * @return array danh sách safeSearchAnnotation cho từng ảnh
     * @throws Exception
     */
    public function detectSafeSearch(array $base64Images): array
    {
        if (!$this->enable) {
            throw new Exception('Google Vision service is disabled.');
        }

        if (empty($base64Images)) {
            throw new Exception('No base64 images provided.');
        }

        // Chuẩn bị payload
        $requests = collect($base64Images)->map(fn($base64) => [
            'image' => ['content' => $base64],
            'features' => [['type' => 'SAFE_SEARCH_DETECTION']],
        ])->values()->all();

        // Gọi API
        $response = Http::post("{$this->apiUrl}?key={$this->apiKey}", [
            'requests' => $requests,
        ]);

        if ($response->failed()) {
            throw new Exception('Google Vision API call failed: ' . $response->body());
        }

        $data = $response->json()['responses'] ?? [];

        // Trả ra danh sách safeSearchAnnotation
        return collect($data)
            ->pluck('safeSearchAnnotation')
            ->filter()
            ->values()
            ->all();
    }
}
