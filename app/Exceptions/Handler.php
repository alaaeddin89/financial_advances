<?php

namespace App\Exceptions;

use App\Traits\ApiResponder;
use App\Traits\APITrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    public function render($request, Throwable $exception)
    {


            if ($request and App::environment(['APP_DEBUG', 'false'])) {
                return $this->handleResponse($exception,$request);
            }
        return parent::render($request, $exception);
    }

    private function handleResponse($exception,$request)
    {
        $message = $exception->getMessage();
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
        if ($exception instanceof UnauthorizedException) {
            $message = trans('messages.unauthorized');
            $statusCode = Response::HTTP_FORBIDDEN;
        }


        if ($exception instanceof AuthenticationException) {
            $message = trans('messages.unauthenticated');
            $statusCode = Response::HTTP_UNAUTHORIZED;
        }


        if ($exception instanceof AuthorizationException) {
            $message = trans('messages.forbidden');
            $statusCode = Response::HTTP_FORBIDDEN;
        }


        if ($exception instanceof NotFoundHttpException) {

            $message = trans('messages.not_found');
            $statusCode = Response::HTTP_NOT_FOUND;
        }


        if ($exception instanceof ModelNotFoundException) {
            $message = trans('messages.element_not_found');
            $statusCode = Response::HTTP_NOT_FOUND;
        }


        if ($exception instanceof MethodNotAllowedHttpException) {
            $message = trans('messages.method_not_allowed');
            $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
        }


        if ($exception instanceof ValidationException) {
            $message = str_replace(':count', count($exception->errors()), trans('messages.validation_errors'));
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        }
//        \response()->json(['success'=>false,'message'=>$message],$statusCode)

        if ($request->ajax()){
        return \response()->json(['success'=>false,'message'=>$message],$statusCode) ;
    }else {

            return redirect()->back()->with("error", $message);
        }
}}
