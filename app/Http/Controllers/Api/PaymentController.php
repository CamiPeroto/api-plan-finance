<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Payments",
 *     description="Gerenciamento de métodos de pagamento"
 * )
 */
class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pagamento",
     *     summary="Listar todos os métodos de pagamento",
     *     tags={"Payments"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de métodos de pagamento"
     *     )
     * )
     */
    public function index()
    {
        $payments = Payment::withCount('relSpentMoney')
            ->orderBy('name')
            ->get();

        return response()->json($payments, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/pagamento",
     *     summary="Criar um novo método de pagamento",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Cartão de Crédito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Método de pagamento criado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function store(Request $request): Response
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payments,name',
        ]);

        $payment = Payment::create($validated);

       return response()->json($payment, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/pagamento/{id}",
     *     summary="Exibir um método de pagamento específico",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do método de pagamento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do método de pagamento"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Método de pagamento não encontrado"
     *     )
     * )
     */
    public function show(Payment $payment): Response
    {
        $payment->loadCount('relSpentMoney');

        return response($payment, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/pagamento/{id}",
     *     summary="Atualizar um método de pagamento",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do método de pagamento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Pix")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Método de pagamento atualizado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Método de pagamento não encontrado"
     *     )
     * )
     */
   public function update(Request $request, string $id)
{
    // Busca o pagamento pelo ID
    $payment = Payment::findOrFail($id);

    // Validação dos dados
    $request->validate([
        'name' => 'required|string|max:255|unique:payments,name,' . $payment->id,
    ]);

    // Atualiza os dados manualmente
    $payment->update([
        'name' => $request->name,
    ]);

    // Retorna JSON com mensagem e dados atualizados
    return response()->json([
        'message' => 'Método de pagamento atualizado com sucesso!',
        'payment' => $payment->fresh(), // fresh() garante os dados atualizados
    ], 200);
}

    /**
     * @OA\Delete(
     *     path="/api/pagamento/{id}",
     *     summary="Excluir um método de pagamento",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do método de pagamento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Método de pagamento excluído com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Método de pagamento não encontrado"
     *     )
     * )
     */
   public function destroy(string $id)
{
    $payment = Payment::findOrFail($id);
    $payment->delete();

    return response()->json([
        'message' => 'Método de pagamento deletado com sucesso!',
    ], 200);
}

}
