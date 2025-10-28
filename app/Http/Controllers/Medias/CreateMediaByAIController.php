<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Services\ImageAIService;
use App\Traits\AWSS3Trait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CreateMediaByAIController extends Controller
{
    use AWSS3Trait;

    public function __invoke(Request $request)
    {
        try {
            $data = [
                "prompt" => $request->input("prompt"),
                "size" => $request->input("size"),
                "style_preset" => $request->input("style_preset")
            ];

            $imageUrl = $request->input("imageUrl");
            $files = $request->input("files");

            if (!$data["prompt"]) {
                return response()->json(['error' => 'Prompt is required'], 400);
            }

            if (!empty($imageUrl) || !empty($files)) {
                $service = new ImageAIService("TEXT_TO_TEXT_FOR_TEXT_TO_IMAGE", "text-to-text");
                $prompt = $service->call($data);
                $data["prompt"] = $prompt;
            }

            $data["image_url"] = $request->input("imageUrl");
            $data["files"] = $request->input("files");
            // Determine keyPrompt based on input type3
            $fileCount = !empty($data['files']) ? count($data['files']) : 0;

            switch (true) {
                case !empty($data['image_url']):
                    $keyPrompt = 'TEXT_IMAGE_TO_IMAGE';
                    break;

                case $fileCount === 2:
                    $keyPrompt = 'TEXT_MERGE_IMAGE_TO_IMAGE';
                    break;

                case $fileCount === 1:
                    $keyPrompt = 'TEXT_IMAGE_TO_IMAGE';
                    break;

                default:
                    $keyPrompt = 'TEXT_TO_IMAGE';
                    break;
            }

            $serviceImage = new ImageAIService($keyPrompt, "text-to-image");
            $response = $serviceImage->call($data);
            // Decode base64 image data
            $imageBytes = base64_decode($response);

            // Generate unique filename
            $filename = 'ai-generated/' . uniqid('gemini_') . '.png';

            // Upload to S3
            Storage::disk('s3')->put($filename, $imageBytes);
            $imageUrl = Storage::disk('s3')->url($filename);

            return response()->json([
                'status' => 'succeeded',
                'imageUrl' => $imageUrl,
                'prompt' => $data["prompt"],
                'sourceImageUrl' => $request->input(key: 'imageUrl'),
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);

        }


        // try {
        //     // // Validate required parameters
        //     $prompt = $request->input('prompt');
        //     $imageUrl = $request->input('imageUrl');

        //     if (!$prompt) {
        //         return response()->json(['error' => 'Prompt is required'], 400);
        //     }

        //     // Google Gemini API endpoint for image generation
        //     $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-preview-image-generation:generateContent";

        //     // Prepare base request structure
        //     $parts = [
        //         ['text' => $prompt]
        //     ];

        //     // If imageUrl is provided, fetch and add the image to the request
        //     if ($imageUrl) {
        //         try {
        //             // Download image from URL
        //             $imageResponse = Http::timeout(30)->get($imageUrl);

        //             if ($imageResponse->successful()) {
        //                 $imageData = $imageResponse->body();
        //                 $base64Image = base64_encode($imageData);

        //                 // Detect MIME type
        //                 $finfo = new \finfo(FILEINFO_MIME_TYPE);
        //                 $mimeType = $finfo->buffer($imageData);

        //                 // Add image to parts (image should come before text for better results)
        //                 array_unshift($parts, [
        //                     'inlineData' => [
        //                         'mimeType' => $mimeType,
        //                         'data' => $base64Image
        //                     ]
        //                 ]);
        //             } else {
        //                 return response()->json(['error' => 'Failed to fetch image from provided URL'], 400);
        //             }
        //         } catch (\Exception $e) {
        //             return response()->json(['error' => 'Error processing image URL: ' . $e->getMessage()], 400);
        //         }
        //     }

        //     // Prepare request data for Gemini API
        //     $data = [
        //         'contents' => [
        //             [
        //                 'parts' => $parts
        //             ]
        //         ],
        //         'generationConfig' => [
        //             'responseModalities' => ['TEXT', 'IMAGE']
        //         ]
        //     ];

        //     // Make request to Google Gemini API
        //     $response = Http::withHeaders([
        //         'Content-Type' => 'application/json',
        //     ])->timeout(120)->post($url . '?key=' . config("common.gemini_api_key"), $data);

        //     if ($response->successful()) {
        //         $responseData = $response->json();

        //         if (isset($responseData['candidates'][0]['content']['parts'])) {
        //             $parts = $responseData['candidates'][0]['content']['parts'];
        //             $imageData = null;
        //             $textResponse = null;

        //             // Tìm image data và text response
        //             foreach ($parts as $part) {
        //                 if (isset($part['inlineData']['data'])) {
        //                     $imageData = $part['inlineData']['data'];
        //                 }
        //                 if (isset($part['text'])) {
        //                     $textResponse = $part['text'];
        //                 }
        //             }

        //             if ($imageData) {
        //                 // Decode base64 image data
        //                 $imageBytes = base64_decode($imageData);

        //                 // Generate unique filename
        //                 $filename = 'ai-generated/' . uniqid('gemini_') . '.png';

        //                 // Upload to S3
        //                 try {
        //                     Storage::disk('s3')->put($filename, $imageBytes);
        //                     $imageUrl = Storage::disk('s3')->url($filename);

        //                     return response()->json([
        //                         'status' => 'succeeded',
        //                         'imageUrl' => $imageUrl,
        //                         'prompt' => $prompt,
        //                         'sourceImageUrl' => $request->input('imageUrl'), // Include source image URL if provided
        //                         'description' => $textResponse
        //                     ], 200);
        //                 } catch (\Exception $e) {
        //                     // Fallback: save to local storage if S3 fails
        //                     $localPath = 'storage/images/' . uniqid('gemini_') . '.png';

        //                     // Ensure directory exists
        //                     if (!file_exists(dirname(public_path($localPath)))) {
        //                         mkdir(dirname(public_path($localPath)), 0755, true);
        //                     }

        //                     file_put_contents(public_path($localPath), $imageBytes);

        //                     return response()->json([
        //                         'status' => 'succeeded',
        //                         'imageUrl' => url($localPath),
        //                         'prompt' => $prompt,
        //                         'sourceImatextResponsegeUrl' => $request->input('imageUrl'), // Include source image URL if provided
        //                         'description' => $textResponse
        //                     ], 200);
        //                 }
        //             } else {
        //                 return response()->json([
        //                     'error' => 'No image data found in response',
        //                     'response' => $responseData
        //                 ], 400);
        //             }
        //         } else {
        //             return response()->json([
        //                 'error' => 'Unexpected response format',
        //                 'response' => $responseData
        //             ], 400);
        //         }
        //     } else {
        //         $errorData = $response->json();
        //         $errorMessage = $errorData['error']['message'] ?? 'Failed to generate image';

        //         return response()->json([
        //             'error' => $errorMessage,
        //             'status_code' => $response->status(),
        //             'details' => $errorData
        //         ], $response->status());
        //     }
        // } catch (\Throwable $th) {
        //     return response()->json(['error' => $th->getMessage()], 500);
        // }
    }
}
