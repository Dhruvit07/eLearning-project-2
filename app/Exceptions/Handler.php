<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
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


    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if (in_array('admin', $exception->guards())) {
            return $request->expectsJson()
                ? response()->json([
                    'message' => $exception->getMessage()
                ], 401)
                : redirect()->guest(route('admin.sign.in'));
        }

        return $request->expectsJson()
            ? response()->json([
                'message' => $exception->getMessage()
            ], 401)
            : redirect()->guest(route('user.sign.in'));
    }
    public function render($request, Throwable $exception)
    {

        if ($this->isHttpException($exception)) {

            if (request()->is('admin/*')) {
                if ($exception->getStatusCode() == 404) {
                    return response()->view('errors.' . '405', [], 404);
                }
            } else {
                if ($exception->getStatusCode() == 404) {
                    return response()->view('errors.' . '404', [], 404);
                }
            }
        }

        return parent::render($request, $exception);
    }
}
