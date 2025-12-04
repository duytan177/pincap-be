<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class MediaIntegrateService
{

    /**
     * Generic method to call any third-party media API
     *
     * @param string $url The API endpoint
     * @param array $params Request parameters
     * @param UploadedFile|null $file Optional file to upload
     * @param string $fileField Name of the file field in multipart/form-data
     * @param string $method HTTP method (POST/GET/PUT)
     * @return array Response array with 'error' and 'data' or 'detail'
     */
    public function callApi(
        string $url,
        array $params = [],
        ?UploadedFile $file = null,
        string $fileField = 'file',
        string $method = 'POST'
    ): array {
        try {
            $request = Http::timeout(30);

            // Attach file if provided
            if ($file) {
                $request = $request->asMultipart()
                    ->attach(
                        $fileField,
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    );
            }

            // Make HTTP request based on the method
            $response = match (strtoupper($method)) {
                'POST' => $request->post($url, $params),
                'PUT' => $request->put($url, $params),
                'GET' => $request->get($url, $params),
                default => $request->post($url, $params)
            };

            // Check for HTTP errors
            if ($response->failed()) {
                return [
                    'error' => true,
                    'message' => "Third-party API error",
                    'detail' => $response->body()
                ];
            }

            // Return successful JSON response
            return [
                'error' => false,
                'data' => $response->json()
            ];
        } catch (\Exception $e) {
            // Handle exceptions
            return [
                'error' => true,
                'message' => 'Exception calling third-party API',
                'detail' => $e->getMessage()
            ];
        }
    }

    /**
     * Search media by image using Python API
     *
     * @param string|null $userId ID of the user performing the search
     * @param UploadedFile $file Image file to search
     * @param int $from Offset for pagination
     * @param int $size Number of items per page
     * @return array API response
     */
    public function searchByImage(?string $userId, UploadedFile $file, int $from = 0, int $size = 10): array
    {
        $url = config('ai_services.base_url') . config("ai_services.endpoint.search_by_image");

        return $this->callApi($url, [
            'user_id' => $userId,
            'from_' => $from,
            // 'size' => $size
        ], $file, 'file', 'POST');
    }

    /**
     * Search media by image using Python API
     *
     * @param string|null $userId ID of the user performing the search
     * @param UploadedFile $file Image file to search
     * @param int $from Offset for pagination
     * @param int $size Number of items per page
     * @return array API response
     */
    public function searchMediaByText(?string $userId, string $text, int $from = 0, int $size = 10): array
    {
        $url = config('ai_services.base_url') . config("ai_services.endpoint.search_by_text");

        return $this->callApi($url, [
            'user_id' => $userId,
            'from_' => $from,
            // 'size' => $size,
            "text" => $text
        ], method: 'POST');
    }


    /**
     * Generic method for calling any custom API
     *
     * @param string $url API endpoint
     * @param array $params Request parameters
     * @param UploadedFile|null $file Optional file to upload
     * @param string $method HTTP method
     * @return array API response
     */
    public function callCustom(string $url, array $params = [], ?UploadedFile $file = null, string $method = 'POST'): array
    {
        return $this->callApi($url, $params, $file, 'file', $method);
    }
}
