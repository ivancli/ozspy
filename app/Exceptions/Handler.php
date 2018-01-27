<?php

namespace OzSpy\Exceptions;

use OzSpy\Exceptions\SocialAuthExceptions\NullEmailException;
use OzSpy\Exceptions\SocialAuthExceptions\UnauthorisedException;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \OzSpy\Exceptions\Crawl\CategoriesNotFoundException::class,
        \OzSpy\Exceptions\Crawl\ProductsNotFoundException::class,
        \OzSpy\Exceptions\Crawl\ScraperNotFoundException::class,
        \OzSpy\Exceptions\Models\DuplicateCategoryException::class,
        \OzSpy\Exceptions\Models\DuplicateProductException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response|string|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        $e = $this->prepareException($exception);
        if ($e instanceof ClientException) {
            return $e->getMessage();
        }
        if ($e instanceof UnauthorisedException || $e instanceof NullEmailException) {
            return redirect()->route('auth.login.get')->withErrors($e->getMessage());
        }

        if ($e instanceof NotFoundHttpException && $request->acceptsJson()) {
            return response()->json(['message' => 'The resource you are looking for cannot be found.'], 404);
        }

        return parent::render($request, $exception);
    }

    public function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->acceptsJson()
            ? response()->json(['message' => 'Unauthenticated. For API requests, please include access token in request header.'], 401)
            : redirect()->guest(route('auth.login.get'));
    }
}
