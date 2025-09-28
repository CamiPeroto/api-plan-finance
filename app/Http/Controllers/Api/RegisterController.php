<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
