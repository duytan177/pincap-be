<?php

namespace App\Exceptions;

class HttpException extends BaseException
{
    /** @var array */
    protected $errors = [];

    public static function make(string $message, int $statusCode = 400, array $errors = [], string $messageCode = ''): self
    {
        $instance = new self($message, $statusCode);
        if ($messageCode !== '') {
            $instance->setMessageCode($messageCode);
        }
        return $instance->setErrors($errors);
    }

    public static function forbidden(string $message = 'Forbidden', array $errors = []): self
    {
        return self::make($message, 403, $errors);
    }

    public static function unauthorized(string $message = 'Unauthorized', array $errors = []): self
    {
        return self::make($message, 401, $errors);
    }

    public static function notFound(string $message = 'Not Found', array $errors = []): self
    {
        return self::make($message, 404, $errors);
    }

    public static function badRequest(string $message = 'Bad Request', array $errors = []): self
    {
        return self::make($message, 400, $errors);
    }

    public static function unprocessable(string $message = 'Unprocessable Entity', array $errors = []): self
    {
        return self::make($message, 422, $errors);
    }

    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}


