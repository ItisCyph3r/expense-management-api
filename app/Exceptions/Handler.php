<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (Throwable $e) {
            if (request()->expectsJson() || request()->is('api/*')) {
                return $this->handleApiException($e);
            }
        });
    }

    private function handleApiException(Throwable $e)
    {
        $status = match(true) {
            $e instanceof ValidationException => 422,
            $e instanceof AuthenticationException => 401,
            $e instanceof ModelNotFoundException => 404,
            $e instanceof NotFoundHttpException => 404,
            $e instanceof QueryException && str_contains($e->getMessage(), 'Duplicate entry') => 409,
            default => 500
        };

        $response = [
            'success' => false,
            'message' => $this->getMessage($e, $status),
        ];

        if ($e instanceof ValidationException) {
            $response['errors'] = $e->errors();
        }

        if (config('app.debug') && $status === 500) {
            $response['debug'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace()
            ];
        }

        return response()->json($response, $status);
    }

    private function getMessage(Throwable $e, int $status): string 
    {
        return match($status) {
            401 => 'Unauthorized',
            404 => 'Resource not found',
            422 => 'Validation failed',
            409 => 'Duplicate entry',
            500 => config('app.debug') ? $e->getMessage() : 'Server Error',
            default => $e->getMessage()
        };
    }
}