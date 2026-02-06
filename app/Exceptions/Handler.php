<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
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
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle authorization exceptions for API
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(
                    ['message' => $e->getMessage() ?: 'This action is unauthorized.'],
                    403
                );
            }
        });

        // Handle Spatie Permission exceptions for API
        $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have the required permission.'
                ], 403);
            }
        });

        // Handle validation exceptions for API
        $this->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'The given data was invalid.',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        // Handle 404 Not Found for API
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource not found.'
                ], 404);
            }
        });

        // Handle general exceptions for API
        $this->renderable(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // Don't handle if already handled above
                if ($e instanceof AuthorizationException || 
                    $e instanceof \Illuminate\Validation\ValidationException ||
                    $e instanceof AuthenticationException ||
                    $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return null;
                }

                // For other exceptions in production
                if (config('app.debug') === false) {
                    return response()->json([
                        'message' => 'Server Error',
                        'error' => 'An error occurred while processing your request.'
                    ], 500);
                }
            }
        });
    }

    /**
     * Convert an authentication exception into a response.
     * For API routes, always return JSON.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // Check if request is to API routes
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated. Please provide a valid authentication token.'
            ], 401);
        }
        
        // For web routes (if any in future)
        return redirect()->guest(route('login'));
    }
}
