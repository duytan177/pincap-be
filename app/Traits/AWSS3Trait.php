<?php

namespace App\Traits;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
// use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Async\Pool;
use Throwable;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;


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
    private function handleMediaFilesWithConcurrency(array $files, int $concurrency = 3)
    {
        $pool = Pool::create()->concurrency($concurrency);

        $results = [];

        foreach ($files as $index => $file) {
            $pool->add(function () use ($index, $file) {
                // === Giả lập upload ===
                $start = microtime(true);
                $duration = rand(2, 5); // giả lập upload tốn 2-5s
                sleep($duration);
                $end = microtime(true);

                return [
                    'file' => $file,
                    'time' => round($end - $start, 2),
                    'thread' => getmypid(),
                ];
            })
                ->then(function ($output) use (&$results, $index) {
                    Log::info("✅ Upload xong {$output['file']} trong {$output['time']}s (PID {$output['thread']})");
                    $results[$index] = $output;
                })
                ->catch(function (Throwable $e) use ($file) {
                    Log::error("❌ Upload lỗi: {$file} | " . $e->getMessage());
                });
        }

        Log::info("🚀 Bắt đầu pool ({$concurrency} job cùng lúc)");
        $pool->wait(); // chặn đến khi tất cả job hoàn tất
        Log::info("🏁 Toàn bộ upload hoàn tất!");

        ksort($results);
        return array_values($results);
    }

    // private function handleMediaFilesWithConcurrency(array $files, int $concurrency = 3)
// {
//     $pool = Pool::create()->concurrency($concurrency);
//     $results = [];

    //     foreach ($files as $index => $file) {
//     $pool->add(function () use ($file, $index) {
//         Log::info("START $index");
//         if ($index == 1) {
//                     sleep(seconds: 5);
//         } else {
//         sleep(seconds: 2);

    //         }
//         Log::info("DONE $index");
//     });
//         // $pool->add(function () use ($file, $index) {
//         //     $fileName = time() . "-" . $file->getClientOriginalName();
//         //     $key = "uploads/test4/" . $fileName;

    //         //     Log::info("START upload file {$index}: {$file->getClientOriginalName()}");

    //         //     // Gọi put() đồng bộ trong process riêng
//         //     Storage::disk('s3')->put($key, file_get_contents($file));
//         //     $url = Storage::disk('s3')->url($key);
//         //     sleep(3); // Giả lập thời gian upload
//         //     Log::info("✅ DONE upload file {$index}: {$file->getClientOriginalName()}");
//         //     return [
//         //         'index' => $index,
//         //         'media_url' => $url,
//         //         'status' => 'success',
//         //     ];
//         // })->then(function ($output) use (&$results, $index) {
//         //     $results[$index] = $output;
//         // })->catch(function ($e) use (&$results, $index, $file) {
//         //     Log::error("❌ FAIL upload {$file->getClientOriginalName()}: " . $e->getMessage());
//         //     $results[$index] = [
//         //         'index' => $index,
//         //         'status' => 'failed',
//         //         'error' => $e->getMessage(),
//         //     ];
//         // });
//     }
// Log::info("→ Code dưới foreach vẫn chạy liền, chưa đợi xong");

    //     $pool->wait();
// Log::info("→ Code dưới foreach vẫn chạy liền, chưa đợi xong");

    // dd($results);
//     ksort($results);
//     return array_values($results);
// }
}
