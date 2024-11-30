<?php
return [
    "timezone_vn" => env("TIMEZONE_VN", "Asia/Ho_Chi_Minh"),
    'avatar_default' => env('AVATAR_DEFAULT', "https://pincap.s3.ap-southeast-1.amazonaws.com/Avatar/avatar-default.jpg"),
    'background_default' => env('BACKGROUND_DEFAULT', "https://pincap.s3.ap-southeast-1.amazonaws.com/Background/background.jpg"),
    "path_verify_email" => env("PATH_VERIFY_EMAIL", "/api/auth/verify-email/"),
    "path_forgot_password" => env("PATH_FORGOT_PASSWORD", "/api/auth/redirect-forgot-password/"),
    "folders_s3" => [
        'image' => "Medias/Image",
        'video' => "Medias/Video",
        "comment" => "Comments/Comment",
        "reply" => "Comments/Reply",
        'avatar' => "Avatar",
        'background' => "Background",
        'icon' => "Icon",
        'logo' => "Logo",
    ],
    "api_key_prodia" => env("API_KEY_PRODIA")
];
