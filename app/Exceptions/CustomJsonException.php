<?php

namespace App\Exceptions;

use App\Traits\ApiResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomJsonException extends Exception
{
    use ApiResponses;

    public function __construct(protected $message = '', protected $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        parent::__construct($message, $status);
        $this->message = $message;
        $this->status = $status;
    }

    public function render($request): JsonResponse
    {
        return $this->errorResponse($this->message, $this->status);
    }
}
