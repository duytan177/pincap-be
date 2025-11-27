<?php
    return [
        "base_url" => env("AI_SERVICE_URL", "http://localhost:8000/"),

        "endpoint" => [
            "text-to-text" => "api/v1/text-to-text/",
            "text-to-image" => "api/v1/image/generate/",
            'search_by_image' => 'api/v1/medias/search_by_image',
        ],
    ];
