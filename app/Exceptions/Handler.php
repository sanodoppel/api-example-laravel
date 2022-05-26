<?php

namespace App\Exceptions;

use App\Http\AppResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  Request  $request
     * @param AuthenticationException $exception
     * @return Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return AppResponse::generate(
            $exception->getMessage(),
            JsonResponse::HTTP_UNAUTHORIZED
        );
    }

    public function renderExceptionResponse($request, Throwable $e): JsonResponse
    {
        return AppResponse::generate(
            config('app.debug') ? $e->getMessage() : ($this->isHttpException($e) ? $e->getMessage() : 'Server Error'),
            $this->isHttpException($e) ?
                $e->getStatusCode() :
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
