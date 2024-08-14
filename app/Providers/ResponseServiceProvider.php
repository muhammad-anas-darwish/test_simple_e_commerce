<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->descriptiveResponseMethods();
    }

    protected function descriptiveResponseMethods()
    {
        $instance = $this;
        Response::macro('ok', function ($data = [], $message = '') {
            $response = [
                'status' => 'success',
                'data' => $data,
            ];
            if ($message !== '') {
                $response['message'] = $message;
            }
            return Response::json($response, 200);
        });

        Response::macro('created', function ($data = [], $message = 'Created Successfully') {
            if (count($data)) {
                return Response::json([
                    'status' => 'success',
                    'message' => $message,
                    'data' => $data,
                ], 201);
            }

            return Response::json(['status' => 'success'], 201);
        });

        Response::macro('noContent', function ($data = []) {
            return Response::json([], 204);
        });

        Response::macro('badRequest', function ($message = 'Validation Failure', $errors = []) use ($instance) {
            return $instance->handleErrorResponse($message, $errors, 400);
        });

        Response::macro('unauthorized', function ($message = 'User unauthorized', $errors = []) use ($instance) {
            return $instance->handleErrorResponse($message, $errors, 401);
        });

        Response::macro('forbidden', function ($message = 'Access denied', $errors = []) use ($instance) {
            return $instance->handleErrorResponse($message, $errors, 403);

        });

        Response::macro('notFound', function ($message = 'Resource not found.', $errors = []) use ($instance) {
            return $instance->handleErrorResponse($message, $errors, 404);
        });

        Response::macro('internalServerError', function ($message = 'Internal Server Error.', $errors = []) use ($instance) {
            return $instance->handleErrorResponse($message, $errors, 500);
        });
    }

    public function handleErrorResponse($message, $errors, $status)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if (count($errors)) {
            $response['errors'] = $errors;
        }

        return Response::json($response, $status);
    }
}
