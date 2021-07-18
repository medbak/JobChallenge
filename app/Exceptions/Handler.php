<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use PDOException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        Log::info($exception);

        $response = [
            'status' => null,
        ];

        $transactionNumber = DB::transactionLevel();
        if ($transactionNumber > 0) {
            DB::rollBack();
        }

       if ($exception instanceof ModelNotFoundException) {
            $response['status'] = Response::HTTP_NOT_FOUND;
            $reflectedObject = new \ReflectionClass(get_class($exception));
            $property = $reflectedObject->getProperty('message');
            $property->setAccessible(true);
            $property->setValue($exception, substr($exception->getModel(), 4).' est inexistant');
            $property->setAccessible(false);
       } elseif ($exception instanceof NotFoundHttpException) {
            $response['status'] = Response::HTTP_NOT_FOUND;
            $response['message'] = 'Route introuvable';
       } elseif ($exception instanceof InsertionException) {
            $response['status'] = Response::HTTP_UNPROCESSABLE_ENTITY;
       } elseif ($exception instanceof \BadMethodCallException) {
            $response['status'] = Response::HTTP_BAD_REQUEST;
       } elseif ($exception instanceof QueryException) {
            $response['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;
            if (app()->environment() == 'local') {
                $response['query'] = $exception->getSql();
                $response['message'] = $exception->getMessage();
            }
       } elseif ($exception instanceof PDOException ||
            $exception instanceof \UnexpectedValueException) {
            $response['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;
       } else {
            $response['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;
       }

        if ($response['status']) {
            if (! array_key_exists('message', $response)) {
                $response['message'] = $exception->getMessage();
            }

            $response['error_code'] = $exception->getCode() ?? '';

            if (app()->environment() == 'local' || app()->environment() == 'testing')
            {
                $response['exception'] = get_class($exception);
                $response['file'] = $exception->getFile() ?? '';
                $response['line'] = $exception->getLine() ?? '';
                $response['stack'] = $exception->getTraceAsString();
            }

            return response()->json($response, $response['status']);
        } else {
            return parent::render($request, $exception);
        }
    }
}
