<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AvailableMoney;
use App\Models\Category;
use App\Models\Payment;
use App\Models\SpentMoney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    private $objSpentMoney;
    private $objAvailableMoney;
    private $objCategory;
    private $objPayment;
    private $carbon;

    public function __construct()
    {
        $this->objSpentMoney = new SpentMoney();
        $this->objAvailableMoney = new AvailableMoney();
        $this->objCategory = new Category();
        $this->objPayment = new Payment();
        $this->carbon = new Carbon();
    }

    /**
 * @OA\Get(
 *     path="/api/despesa",
 *     summary="Lista despesas do mês atual",
 *     tags={"Finance"},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de despesas com saldo disponível",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="finances", type="array", @OA\Items(ref="#/components/schemas/Finance")),
 *             @OA\Property(property="available_money", type="array", @OA\Items(ref="#/components/schemas/AvailableMoney")),
 *             @OA\Property(property="diff", type="number", format="float", example=250.50)
 *         )
 *     )
 * )
 */
    public function index()
    {
        $currentMonth = $this->carbon->month;
        $currentYear = $this->carbon->year;

        $finances = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->with(['relCategory', 'relPayment'])
            ->orderBy('date', 'desc')
            ->paginate(6);

        $available_moneys = $this->objAvailableMoney
            ->where('user_id', Auth::id())
            ->paginate(6);

        $diff = $available_moneys->sum('to_spend') - $finances->sum('value');

        return response()->json([
            'finances' => $finances,
            'available_money' => $available_moneys,
            'diff' => $diff,
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/despesa",
     *     summary="Cria nova despesa",
     *     tags={"Finance"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","value","date","available_money_id"},
     *             @OA\Property(property="name", type="string", example="Compra supermercado"),
     *             @OA\Property(property="description", type="string", example="Compra semanal"),
     *             @OA\Property(property="value", type="number", format="float", example=150.75),
     *             @OA\Property(property="date", type="string", format="date", example="2025-09-28"),
     *             @OA\Property(property="available_money_id", type="integer", example=1),
     *             @OA\Property(property="categories_id", type="integer", example=2),
     *             @OA\Property(property="payments_id", type="integer", example=1),
     *             @OA\Property(property="payable", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Despesa criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Finance")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'value' => 'required|numeric',
            'date' => 'required|date',
            'available_money_id' => 'required|integer',
        ]);

        $payable = $request->has('payable') ? 1 : 0;

        $finance = $this->objSpentMoney->create([
            'user_id' => Auth::id(),
            'available_money_id' => $request->available_money_id,
            'categories_id' => $request->categories_id,
            'payments_id' => $request->payments_id ?? null,
            'name' => $request->name,
            'description' => $request->description,
            'payable' => $payable,
            'value' => $request->value,
            'date' => $request->date,
        ]);

        return response()->json($finance, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/despesa/{id}",
     *     summary="Mostra detalhes de uma despesa",
     *     tags={"Finance"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da despesa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Despesa encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Finance")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Despesa não encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $finance = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->with(['relCategory', 'relPayment'])
            ->find($id);

        if (!$finance) {
            return response()->json(['error' => 'Despesa não encontrada'], 404);
        }

        return response()->json($finance, 200);
    }

   
    /**
     * @OA\Put(
     *     path="/api/despesa/{id}",
     *     summary="Atualiza uma despesa",
     *     tags={"Finance"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da despesa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","value","date","available_money_id"},
     *             @OA\Property(property="name", type="string", example="Compra supermercado"),
     *             @OA\Property(property="description", type="string", example="Compra semanal"),
     *             @OA\Property(property="value", type="number", format="float", example=200.00),
     *             @OA\Property(property="date", type="string", format="date", example="2025-10-01"),
     *             @OA\Property(property="available_money_id", type="integer", example=1),
     *             @OA\Property(property="categories_id", type="integer", example=3),
     *             @OA\Property(property="payments_id", type="integer", example=2),
     *             @OA\Property(property="payable", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Despesa atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Finance")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Despesa não encontrada"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $finance = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$finance) {
            return response()->json(['error' => 'Despesa não encontrada'], 404);
        }

        $finance->update([
            'name' => $request->name,
            'description' => $request->description,
            'value' => $request->value,
            'date' => $request->date,
            'categories_id' => $request->categories_id,
            'payments_id' => $request->payments_id,
            'payable' => $request->payable ? 1 : 0,
            'available_money_id' => $request->available_money_id,
        ]);

        return response()->json($finance, 200);
    }

 /**
     * @OA\Delete(
     *     path="/api/despesa/{id}",
     *     summary="Deleta uma despesa",
     *     tags={"Finance"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da despesa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Despesa deletada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Despesa deletada com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Despesa não encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $finance = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$finance) {
            return response()->json(['error' => 'Despesa não encontrada'], 404);
        }

        $finance->delete();

        return response()->json(['message' => 'Despesa deletada com sucesso'], 200);
    }

    // POST /api/despesa/search
    public function search(Request $request)
    {
        $search = $request->input('search');

        $finances = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('value', 'like', "%$search%")
                    ->orWhereHas('relCategory', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            })
            ->with(['relCategory', 'relPayment'])
            ->paginate(5);

        $availableMoney = $this->objAvailableMoney
            ->where('user_id', Auth::id())
            ->get();

        $diff = $availableMoney->sum('to_spend') - $finances->sum('value');

        return response()->json([
            'finances' => $finances,
            'available_money' => $availableMoney,
            'diff' => $diff,
        ], 200);
    }
}
