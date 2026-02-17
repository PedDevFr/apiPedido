<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use ApiResponse;

    /**
     * Listar clientes con paginación.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 15), 100);
        $clients = Client::query()
            ->orderBy('name')
            ->paginate($perPage);

        return $this->successResponse($clients, 'Lista de clientes');
    }

    /**
     * Crear cliente.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = Client::create($request->validated());

        return $this->createdResponse($client, 'Cliente creado correctamente');
    }

    /**
     * Mostrar cliente.
     */
    public function show(Client $client): JsonResponse
    {
        $client->load('orders');

        return $this->successResponse($client, 'Detalle del cliente');
    }

    /**
     * Actualizar cliente.
     */
    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        $client->update($request->validated());

        return $this->successResponse($client->fresh(), 'Cliente actualizado correctamente');
    }

    /**
     * Eliminar cliente (soft delete).
     */
    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return $this->successResponse(null, 'Cliente eliminado correctamente');
    }
}
