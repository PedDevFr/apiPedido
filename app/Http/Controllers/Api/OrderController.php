<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    /**
     * Listar pedidos con filtros y paginación.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::query()->with(['client', 'user', 'products']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min($request->input('per_page', 15), 100);
        $orders = $query->orderByDesc('order_date')->paginate($perPage);

        return $this->successResponse($orders, 'Lista de pedidos');
    }

    /**
     * Crear pedido.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $products = $validated['products'];
        unset($validated['products']);

        $order = Order::create([
            ...$validated,
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $request->user()->id,
            'status' => $validated['status'] ?? Order::STATUS_PENDING,
        ]);

        $syncData = [];
        $total = 0;
        foreach ($products as $item) {
            $subtotal = $item['quantity'] * $item['price'];
            $total += $subtotal;
            $syncData[$item['product_id']] = [
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ];
        }
        $order->products()->sync($syncData);
        $order->update(['total' => $total]);

        $order->load(['client', 'user', 'products']);

        return $this->createdResponse($order, 'Pedido creado correctamente');
    }

    /**
     * Mostrar pedido.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['client', 'user', 'products']);

        return $this->successResponse($order, 'Detalle del pedido');
    }

    /**
     * Actualizar pedido.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();

        if (isset($validated['products'])) {
            $products = $validated['products'];
            unset($validated['products']);

            $syncData = [];
            $total = 0;
            foreach ($products as $item) {
                $total += $item['quantity'] * $item['price'];
                $syncData[$item['product_id']] = [
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ];
            }
            $order->products()->sync($syncData);
            $validated['total'] = $total;
        }

        $order->update($validated);

        $order->load(['client', 'user', 'products']);

        return $this->successResponse($order->fresh(), 'Pedido actualizado correctamente');
    }

    /**
     * Eliminar pedido (soft delete).
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return $this->successResponse(null, 'Pedido eliminado correctamente');
    }
}
