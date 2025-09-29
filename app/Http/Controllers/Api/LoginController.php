<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Login de usuário",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="usuario@exemplo.com"),
 *             @OA\Property(property="password", type="string", example="12345678")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login realizado com sucesso"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="E-mail ou senha inválidos"
 *     )
 * )
 */


class LoginController extends Controller
{


    /**
     * Login e geração de token
     */

    public function store(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'E-mail ou senha inválidos!',
            ], 401);
        }

        // Gera um token pessoal para API
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'user'    => $user,
            'token'   => $token,
        ], 200);
    }

    /**
     * Logout e revogação do token
     */

        /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout de usuário",
     *     tags={"Auth"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */

    public function destroy(Request $request)
    {
        // Revoga apenas o token usado na requisição
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso!',
        ], 200);
    }
}
