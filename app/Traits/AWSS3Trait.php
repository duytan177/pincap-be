<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

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
}
