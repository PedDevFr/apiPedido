<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    /**
     * Listar productos con búsqueda y paginación.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $perPage = min($request->input('per_page', 15), 100);
        $products = $query->orderBy('name')->paginate($perPage);

        return $this->successResponse($products, 'Lista de productos');
    }

    /**
     * Crear producto.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return $this->createdResponse($product, 'Producto creado correctamente');
    }

    /**
     * Mostrar producto.
     */
    public function show(Product $product): JsonResponse
    {
        return $this->successResponse($product, 'Detalle del producto');
    }

    /**
     * Actualizar producto.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return $this->successResponse($product->fresh(), 'Producto actualizado correctamente');
    }

    /**
     * Eliminar producto (soft delete).
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return $this->successResponse(null, 'Producto eliminado correctamente');
    }
}
