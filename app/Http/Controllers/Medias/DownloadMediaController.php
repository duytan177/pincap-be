<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DownloadMediaController extends Controller
{
    const MEDIAS = "Medias/";
    public function __invoke(Request $request)
    {
        $urls = $request->input('urls');

        if (!$urls || !is_array($urls)) {
            return response()->json(['error' => 'No URLs provided or invalid format'], 400);
        }

        $pathsFromS3 = $this->extractPathFromS3URLs($urls);

        if (count($pathsFromS3) == 1) {
            $path = $pathsFromS3[0];

            if (!Storage::disk('s3')->exists($path)) {
                return response()->json(['error' => 'File does not exist on S3: ' . $path], 404);
            }

            return Storage::disk('s3')->download($path);
        }

        $zip = new ZipArchive();
        $zipFileName = "downloads-" . time() . ".zip";

        if ($zip->open(storage_path($zipFileName), ZipArchive::CREATE) === true) {
            foreach ($pathsFromS3 as $path) {
                if (Storage::disk('s3')->exists($path)) {
                    $fileContent = Storage::disk('s3')->get($path);
                    $zip->addFromString(basename($path), $fileContent);
                } else {
                    return response()->json(['error' => 'File does not exist on S3: ' . $path], 404);
                }
            }
            $zip->close();
        }

        return response()->download(storage_path($zipFileName))->deleteFileAfterSend(true);
    }

    protected function extractPathFromS3URLs($mediaURLs)
    {
        $result = [];
        foreach ($mediaURLs as $mediaURL) {
            $parsedUrl = parse_url($mediaURL);
            $path = substr($parsedUrl['path'], strpos($parsedUrl['path'], self::MEDIAS));
            $result[] = $path;
        }
        return $result;
    }
}
