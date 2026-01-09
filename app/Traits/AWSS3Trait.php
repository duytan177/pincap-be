<?php

namespace App\Traits;

use App\Enums\Album_Media\MediaType;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;


trait AWSS3Trait
{
    const IMAGE = "IMAGE";
    const VIDEO = "VIDEO";
    const COMMENT = "comment";
    const REPLY = "reply";


    private S3Client $s3Client;

    // Khởi tạo S3Client khi trait được dùng
    private function initS3Client(): void
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);
    }

    /**
     * Sanitize tên file: thay thế ký tự đặc biệt bằng "_"
     *
     * @param string $fileName
     * @return string
     */
    private function sanitizeFileName(string $fileName): string
    {
        // Giữ lại chữ cái, số, dấu chấm, dấu gạch ngang, dấu gạch dưới
        // Thay thế tất cả ký tự đặc biệt khác bằng "_"
        $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
        
        // Loại bỏ nhiều dấu gạch dưới liên tiếp thành một
        $sanitized = preg_replace('/_+/', '_', $sanitized);
        
        return $sanitized;
    }

    public function uploadToS3($file, $type)
    {
        if (!isset($this->s3Client)) {
            $this->initS3Client();
        }

        $originalName = $file->getClientOriginalName();
        $sanitizedName = $this->sanitizeFileName($originalName);
        $fileName = time() . "-" . $sanitizedName;
        $mediaFolder = config("common.folders_s3.$type");
        $filePath = $mediaFolder . "/" . $fileName;

        try {
            $result = $this->s3Client->upload(
                config('filesystems.disks.s3.bucket'),
                $filePath,
                fopen($file->getRealPath(), 'rb'), // stream thay vì load toàn bộ file
            );

            return $result->get('ObjectURL'); // trả về URL S3
        } catch (AwsException $e) {
            Log::error("❌ Upload lỗi file {$fileName}: " . $e->getMessage());
            throw $e;
        }
        // Storage::disk('s3')->put($filePath, file_get_contents($file));

        // return Storage::disk('s3')->url($filePath);
    }

    /**
     * Tạo presigned URL cho một file trên S3
     *
     * @param string $filePath path trong bucket
     * @param int $expires Thời gian hết hạn (giây), mặc định 3600 = 1 giờ
     * @return string
     */
    public function getPresignedUrl(string $filePath, int $expires = 3600): string
    {
        if (!isset($this->s3Client)) {
            $this->initS3Client();
        }

        try {
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => $filePath,
            ]);

            $request = $this->s3Client->createPresignedRequest($cmd, "+{$expires} seconds");

            return (string) $request->getUri();
        } catch (AwsException $e) {
            Log::error("❌ Presign URL lỗi file {$filePath}: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteFromS3($fileUrl)
    {
        if (!isset($this->s3Client)) {
            $this->initS3Client();
        }

        $path = parse_url(urldecode($fileUrl), PHP_URL_PATH);
        $path = ltrim($path, '/');
        try {
            $this->s3Client->deleteObject([
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => $path,
            ]);
        } catch (AwsException $e) {
            Log::error("❌ Delete lỗi file {$fileUrl}: " . $e->getMessage());
        }

        // Storage::disk('s3')->delete($path);
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


    // ========================== UPLOAD URL ==========================

    private function uploadUrlToS3(string $url, string $type): string
    {
        if (!isset($this->s3Client))
            $this->initS3Client();

        $originalName = basename(parse_url($url, PHP_URL_PATH));
        $sanitizedName = $this->sanitizeFileName($originalName);
        $fileName = time() . '-' . $sanitizedName;
        $mediaFolder = config("common.folders_s3.$type");
        $filePath = "$mediaFolder/$fileName";

        try {
            $stream = fopen($url, 'rb');
            if (!$stream)
                throw new \Exception("Cannot open URL: $url");

            $result = $this->s3Client->upload(
                config('filesystems.disks.s3.bucket'),
                $filePath,
                $stream,
            );

            // fclose($stream);
            return $result->get('ObjectURL');
        } catch (AwsException $e) {
            Log::error("❌ Upload lỗi file {$fileName} từ URL {$url}: " . $e->getMessage());
            throw $e;
        }
    }

    private function handleMediaUrl(string $url): array
    {
        $headers = get_headers($url, 1);
        $mimeType = $headers['Content-Type'] ?? 'application/octet-stream';
        if (is_array($mimeType)) {
            $mimeType = $mimeType[0];
        }
        [$type, $mediaType] = $this->getTypeMedia($mimeType);
        $mediaUrl = $this->uploadUrlToS3($url, $mediaType);

        return [
            'type' => $type,
            'media_url' => $mediaUrl,
        ];
    }

    public function handleMediaUrlsWithConcurrency(array $urls, int $concurrency = 3): array
    {
        $results = [];
        Log::info("Starting URL upload process with concurrency: {$concurrency}");

        $chunks = array_chunk($urls, $concurrency, true);

        foreach ($chunks as $batchIndex => $batch) {
            Log::info("Running batch {$batchIndex} (up to {$concurrency} URLs in parallel)");

            $promises = [];

            foreach ($batch as $index => $url) {
                $promises[$index] = \GuzzleHttp\Promise\Create::promiseFor(null)
                    ->then(function () use ($url, $index, &$results) {
                        $results[$index] = $this->handleMediaUrl($url);
                        Log::info("✅ Finished uploading URL #{$index}");
                    })
                    ->otherwise(function ($e) use ($index, $url) {
                        Log::error("❌ Upload lỗi URL #{$index}: {$url} | " . $e->getMessage());
                        throw $e;
                    });
            }

            Utils::settle($promises)->wait();
            unset($promises);
            gc_collect_cycles();
            usleep(100_000);
        }

        ksort($results);
        Log::info("🏁 All URL uploads completed successfully! Total: " . count($results));

        return array_values($results);
    }
}
