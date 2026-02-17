<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Respuesta JSON exitosa.
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Operación exitosa',
        int $code = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta JSON de error.
     */
    protected function errorResponse(
        string $message = 'Error en la operación',
        int $code = 400,
        ?array $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta para recurso creado.
     */
    protected function createdResponse(mixed $data = null, string $message = 'Creado correctamente'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Respuesta para recurso no encontrado.
     */
    protected function notFoundResponse(string $message = 'Recurso no encontrado'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Respuesta para acceso no autorizado.
     */
    protected function unauthorizedResponse(string $message = 'No autorizado'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Respuesta para acceso prohibido.
     */
    protected function forbiddenResponse(string $message = 'Acceso denegado'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }
}
