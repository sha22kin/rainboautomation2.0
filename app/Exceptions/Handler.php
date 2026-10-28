<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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

        // CMS (/admin) keeps its own error pages; the public website uses resources/views/errors
        $this->renderable(function (HttpExceptionInterface $e, $request) {
            $view = 'cms.errors.'.$e->getStatusCode();
            if($request->is('admin*') && !$request->expectsJson() && view()->exists($view)){
                return response()->view($view, ['exception' => $e], $e->getStatusCode(), $e->getHeaders());
            }
        });
    }
}
