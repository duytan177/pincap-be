<?php

/**
 * responseWithMessage
 * @param string
 * @return array
 */
if (!function_exists('responseWithMessage')) {
    function responseWithMessage(string $code)
    {
        return [
            'message' => __($code, [], 'en'),
        ];
    }
}
