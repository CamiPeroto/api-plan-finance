<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AvailableMoney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailableMoneyController extends Controller
{
    private $objAvailableMoney;

    public function __construct()
    {
        $this->objAvailableMoney = new AvailableMoney();
    }
    /**
     * @OA\Get(
     *     path="/api/entrada",
     *     summary="Lista entradas do mês atual com paginação",
     *     tags={"AvailableMoney"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de entradas",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AvailableMoney"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $availableMoneys = $this->objAvailableMoney
            ->where('user_id', Auth::id())
            // ->whereMonth('date', $currentMonth)
            // ->whereYear('date', $currentYear)
            ->orderBy('date', 'desc')
            ->get();

        $availableMoneys->each(function ($entry) {
            $entry->formatted_date = Carbon::parse($entry->date)->format('d/m/Y');
        });

        return response()->json($availableMoneys, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/entrada",
     *     summary="Cria nova entrada",
     *     tags={"AvailableMoney"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","to_spend","date"},
     *             @OA\Property(property="name", type="string", example="Salário"),
     *             @OA\Property(property="to_spend", type="number", example=2500.00),
     *             @OA\Property(property="date", type="string", format="date", example="2025-09-28")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Entrada adicionada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/AvailableMoney")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'to_spend' => 'required|numeric',
            'date'     => 'required|date',
        ]);

        $entry = $this->objAvailableMoney->create([
            'user_id'  => Auth::id(),
            'name'     => $request->name,
            'to_spend' => $request->to_spend,
            'date'     => $request->date,
        ]);

        return response()->json([
            'message' => 'Entrada adicionada com sucesso!',
            'entry'   => $entry,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/entrada/{id}",
     *     summary="Mostra uma entrada específica",
     *     tags={"AvailableMoney"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entrada encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/AvailableMoney")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entrada não encontrada"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function show($id)
    {
        $entry = $this->objAvailableMoney
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $entry->formatted_date = Carbon::parse($entry->date)->format('d/m/Y');

        return response()->json($entry, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/entrada/{id}",
     *     summary="Atualiza uma entrada existente",
     *     tags={"AvailableMoney"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","to_spend","date"},
     *             @OA\Property(property="name", type="string", example="Salário Atualizado"),
     *             @OA\Property(property="to_spend", type="number", example=3000.00),
     *             @OA\Property(property="date", type="string", format="date", example="2025-09-28")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entrada atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/AvailableMoney")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entrada não encontrada"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $entry = $this->objAvailableMoney
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'to_spend' => 'required|numeric',
            'date'     => 'required|date',
        ]);

        $entry->update([
            'name'     => $request->name,
            'to_spend' => $request->to_spend,
            'date'     => $request->date,
        ]);

        return response()->json([
            'message' => 'Entrada atualizada com sucesso!',
            'entry'   => $entry,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/entrada/{id}",
     *     summary="Deleta uma entrada",
     *     tags={"AvailableMoney"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entrada deletada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entrada não encontrada"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $entry = $this->objAvailableMoney
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $entry->delete();

        return response()->json([
            'message' => 'Entrada deletada com sucesso!',
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/entrada/search",
     *     summary="Busca entradas por nome ou valor",
     *     tags={"AvailableMoney"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", example="Salário")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultados da busca",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AvailableMoney"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $availableMoneys = $this->objAvailableMoney
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('to_spend', 'like', '%' . $search . '%');
            })
            ->paginate(5);

        return response()->json($availableMoneys, 200);
    }
}
