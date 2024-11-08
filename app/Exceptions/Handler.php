<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
{
    // Check if the request expects JSON (API request) and if the exception is a 404 error
    // if ($exception instanceof NotFoundHttpException && $request->expectsJson()) {
    //     return response()->json([
    //         'status' => 404,
    //         "success" => false,
    //         'message' => 'Route not found',
    //     ], 404);
    // }
    
    if ($request->expectsJson()) {
        return response()->json([
            'error' => $exception->getMessage()
        ], 500);
    }

    return parent::render($request, $exception);
}
}
