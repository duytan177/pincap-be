<?php

namespace App\Traits;

use App\Enums\Album_Media\MediaType;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;


trait AWSS3Trait
{
    const IMAGE = "IMAGE";
    const VIDEO = "VIDEO";
    const COMMENT = "comment";
    const REPLY = "reply";

    public function uploadToS3($file, $type)
    {
        $fileName = time() . "-" . $file->getClientOriginalName();
        $mediaFolder = config("common.folders_s3.$type");
        $filePath = $mediaFolder . "/" . $fileName;

        Storage::disk('s3')->put($filePath, file_get_contents($file));

        return Storage::disk('s3')->url($filePath);
    }

    public function deleteFromS3($fileUrl)
    {
        $path = parse_url(urldecode($fileUrl), PHP_URL_PATH);
        $path = ltrim($path, '/');
        Storage::disk('s3')->delete($path);
    }

    private function handleMediaFile($file)
    {
        [$type, $mediaType] = $this->getTypeMedia($file->getMimeType());
        $mediaUrl = $this->uploadToS3($file, $mediaType);

        return [
            'type' => $type,
            'media_url' => $mediaUrl,
        ];
    }

    private function getTypeMedia($mimeType)
    {
        $image = strtolower(self::IMAGE);
        $video = strtolower(self::VIDEO);

        if (str_starts_with($mimeType, $image)) {
            $type = MediaType::getValue(self::IMAGE);
            $typeName = $image;
        } else {
            $type = MediaType::getValue(self::VIDEO);
            $typeName = $video;
        }

        return [$type, $typeName];
    }

    private function handleMediaFilesWithConcurrency(array $files, int $concurrency = 3)
    {
        $results = [];

        Log::info("Starting upload process with concurrency: {$concurrency}");

        // Split files into batches to control concurrency
        $fileChunks = array_chunk($files, $concurrency, true);

        foreach ($fileChunks as $batchIndex => $batchFiles) {
            Log::info("Running batch {$batchIndex} (up to {$concurrency} files in parallel)");

            $promises = [];

            foreach ($batchFiles as $index => $file) {
                $fileName = $file->getClientOriginalName();

                // Create a promise that handles actual upload (S3 or other)
                $promises[$index] = \GuzzleHttp\Promise\Create::promiseFor(null)
                    ->then(function () use ($file, $index, $fileName, &$results) {
                        $result = $this->handleMediaFile($file);

                        Log::info("✅ Finished uploading file #{$index}: {$fileName}");
                        $results[$index] = $result;
                    })
                    ->otherwise(function ($e) use ($index, $fileName) {
                        Log::error("❌ Upload lỗi file {$index}: {$fileName} | " . $e->getMessage());
                        // Re-throw the exception to reject the promise
                        throw $e;
                    });
            }

            // 2️⃣ Wait for all promises in this batch to complete
            Utils::settle($promises)->wait();

            // Free memory after each batch
            unset($promises);
            gc_collect_cycles();

            Log::info("✅ Completed batch {$batchIndex}");
            usleep(100_000); // small pause between batches for stability/log readability
        }

        // Sort results by original file index to maintain order
        ksort($results);

        Log::info("🏁 All uploads completed successfully! Total files: " . count($results));

        return array_values($results);
    }
}
