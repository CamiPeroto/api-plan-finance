<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Cadastro de usuário",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", example="Camila Silva"),
 *             @OA\Property(property="email", type="string", format="email", example="camila@exemplo.com"),
 *             @OA\Property(property="password", type="string", format="password", example="12345678")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Registro realizado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Registro realizado com sucesso!"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Camila Silva"),
 *                 @OA\Property(property="email", type="string", example="camila@exemplo.com")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erro de validação",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="name", type="array",
 *                     @OA\Items(type="string", example="O campo nome é obrigatório")
 *                 ),
 *                 @OA\Property(property="email", type="array",
 *                     @OA\Items(type="string", example="Esse e-mail já está cadastrado no sistema")
 *                 ),
 *                 @OA\Property(property="password", type="array",
 *                     @OA\Items(type="string", example="A senha deve ter pelo menos 8 caracteres")
 *                 )
 *             )
 *         )
 *     )
 * )
 */

class RegisterController extends Controller
{
    /**
     * Criação de usuário via API
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ], [
            'name.required'     => 'O campo nome é obrigatório',
            'email.required'    => 'O campo e-mail é obrigatório',
            'email.unique'      => 'Esse e-mail já está cadastrado no sistema',
            'password.required' => 'O campo senha é obrigatório',
            'password.min'      => 'A senha deve ter pelo menos 8 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'message' => 'Registro realizado com sucesso!',
            'user'    => $user,
        ], 201);
    }
}
