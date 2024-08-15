<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    public function successResponse($data = [], $message = '', $status = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    public function noContentResponse(): JsonResponse
    {
        return response()->json([], 204);
    }
    

    public function errorResponse($message, $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    public function paginatedResponse($items, $resourceClass, $message = 'Data retrieved successfully'): JsonResponse
    {
        return $this->successResponse([
            'items' => $resourceClass::collection($items),
            'pagination' => [
                'total' => $items->total(),
                'count' => $items->count(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'total_pages' => $items->lastPage(),
                'next_page_url' => $items->nextPageUrl(),
                'previous_page_url' => $items->previousPageUrl(),
            ]
        ], $message);
    }
}
