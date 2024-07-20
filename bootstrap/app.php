<?php

use Illuminate\Http\Request;
use App\Exceptions\BaseException;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Response as HttpStatusCode;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
            Route::middleware('web')->group(base_path('routes/web.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            // 'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception|Error $exception, Request $request) {
            $statusCode = 400;
            $errors = [];
            $message = 'errors unexpected';

            switch (true) {
                case $exception instanceof ValidationException:
                    $errorMsg = collect($exception->errors())->first();
                    $message = !empty($errorMsg) ? reset($errorMsg) : __('messages.errors.input');
                    $errors = $exception->errors();
                    $statusCode = HttpStatusCode::HTTP_UNPROCESSABLE_ENTITY;
                    break;

                case $exception instanceof AuthenticationException:
                    $message = $exception->getMessage();
                    $statusCode = HttpStatusCode::HTTP_UNAUTHORIZED;
                    break;

                case $exception->getPrevious() instanceof ModelNotFoundException:
                    $message = "model not found";
                    $statusCode = HttpStatusCode::HTTP_NOT_FOUND;
                    break;

                case $exception instanceof MethodNotAllowedHttpException:
                    $message = $exception->getMessage();
                    $statusCode = HttpStatusCode::HTTP_METHOD_NOT_ALLOWED;
                    break;

                case $exception instanceof NotFoundHttpException:
                case $exception instanceof AccessDeniedHttpException:
                case $exception instanceof AuthorizationException:
                    $message = $exception->getMessage();
                    $statusCode = HttpStatusCode::HTTP_NOT_FOUND;
                    break;

                case $exception instanceof TokenExpiredException:
                    $message = $exception->getMessage();
                    $statusCode = HttpStatusCode::HTTP_UNAUTHORIZED;
                    break;

                case $exception instanceof BaseException:
                    $message = $exception->getMessage();
                    $statusCode = $exception->getCode();
                    break;

                case $exception instanceof UnauthorizedException:
                    $message = $exception->getMessage();
                    $statusCode = HttpStatusCode::HTTP_UNAUTHORIZED;
                    break;

                case $exception instanceof TokenMismatchException:
                    $message = $exception->getMessage();
                    $statusCode = 419;
                    break;

                case $exception instanceof TypeError:
                    $message = $exception->getMessage();
                    $statusCode = HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR;
                    break;

                default:
                    $message = $exception->getMessage();
                    $statusCode = HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR;
                    break;
            }

            if ($request->is('*')) {
                return response()->json([
                    'errors' => $errors,
                    'message' => $message,
                ], $statusCode);
            }
        });
    })->withSchedule(function (Schedule $schedule) {

    })->create();
