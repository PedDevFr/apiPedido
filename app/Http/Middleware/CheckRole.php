<?php

namespace App\Http\Middleware;

use App\Http\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponse;

    /**
     * Rutas excluidas del middleware (pueden ser accedidas sin rol específico).
     *
     * @var array<string>
     */
    protected array $except = [];

    /**
     * Handle an incoming request.
     *
     * @param  array<string>  $roles  Roles permitidos (admin, vendedor, etc.)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return $this->unauthorizedResponse('No autenticado.');
        }

        $user = $request->user();

        // Soporta role:admin,vendedor (un string) o role:admin vendedor (múltiples)
        $allowedRoles = [];
        foreach ($roles as $roleParam) {
            $allowedRoles = array_merge(
                $allowedRoles,
                array_map('trim', explode(',', $roleParam))
            );
        }

        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return $this->forbiddenResponse(
            'No tiene permisos para acceder a este recurso. Roles requeridos: ' . implode(', ', $allowedRoles)
        );
    }
}
