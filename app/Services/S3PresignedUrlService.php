<?php

namespace App\Services;

use App\Traits\AWSS3Trait;

class S3PresignedUrlService
{
    use AWSS3Trait;

    /**
     * Convert media_url (có thể là string, array, hoặc JSON string) sang presigned URLs
     *
     * @param mixed $mediaUrl Có thể là string, array, hoặc JSON string
     * @param int $expires Thời gian hết hạn (giây), mặc định 3600 = 1 giờ
     * @return mixed Cùng format với input nhưng đã convert sang presigned URLs
     */
    public static function convert($mediaUrl, int $expires = 3600)
    {
        $instance = new self();
        return $instance->convertMediaUrlToPresigned($mediaUrl, $expires);
    }
}

