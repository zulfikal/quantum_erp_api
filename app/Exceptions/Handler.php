<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
        // Custom handler for Spatie permission exceptions
        $this->renderable(function (UnauthorizedException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have the necessary permissions to access this resource.',
            ], 403);
        });
        
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->json()) {
                return response()->json([
                    'error' => 'Resource not found',
                ], 404);
            }

            throw $e;
        });
        
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
