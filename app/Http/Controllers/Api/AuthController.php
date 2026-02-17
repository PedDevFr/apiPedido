<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Registro de nuevo usuario.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        // Asignar rol vendedor por defecto
        $vendedorRole = Role::where('name', Role::VENDEDOR)->first();
        if ($vendedorRole) {
            $user->roles()->attach($vendedorRole);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->createdResponse([
            'user' => $user->load('roles'),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Usuario registrado correctamente');
    }

    /**
     * Login de usuario.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no son correctas.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Revocar tokens previos (opcional: un solo dispositivo)
        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->load('roles'),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login exitoso');
    }

    /**
     * Cerrar sesión (revocar token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Sesión cerrada correctamente');
    }

    /**
     * Obtener usuario autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            $request->user()->load('roles'),
            'Usuario actual'
        );
    }
}
