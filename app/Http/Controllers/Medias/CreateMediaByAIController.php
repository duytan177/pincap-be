<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\CreateMediaRequest;
use App\Traits\AWSS3Trait;
use Illuminate\Support\Facades\Http;

class CreateMediaByAIController extends Controller
{
    use AWSS3Trait;

    public function __invoke(CreateMediaRequest $request)
    {
        try {
            $imageUrl = $request->input("imageUrl");
            $imageData = $request->input("imageData");
            $data = [
                'prompt' =>  $request->input('prompt'),
                'height' => $request->input('height'),
                'width' => $request->input('width'),
                'style_preset' => $request->input("style_preset"),
                'steps' => 20,
            ];
            if ($imageUrl || $imageData) {
                $url = "https://api.prodia.com/v1/sd/transform";
                if ($imageUrl){
                    $data["imageUrl"] = $imageUrl;
                } else {
                    $data["imageData"] = $imageData;
                }
            } else {
                $url = "https://api.prodia.com/v1/sd/generate";
            }
            $response = Http::withHeaders([
                'X-Prodia-Key' => config("common.api_key_prodia"),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, $data);
            if ($response->successful()) {
                $jobId = $response->json()['job'];
                do {
                    $jobResponse = Http::withHeaders([
                        'X-Prodia-Key' => config("common.api_key_prodia"),
                        'Accept' => 'application/json',
                    ])->get("https://api.prodia.com/v1/job/{$jobId}");
                    $status = $jobResponse->json()['status'] ?? null;
                } while ($status != 'succeeded');
                if ($jobResponse->successful()) {
                    return response()->json($jobResponse->json(), 200);
                } else {
                    // Xử lý khi yêu cầu lấy job không thành công
                    return response()->json(['error' => 'Failed to retrieve job details'], $jobResponse->status());
                }
            } else {
                // Xử lý khi yêu cầu tạo job không thành công
                return response()->json(['error' => 'Failed to create job'], $response->status());
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
