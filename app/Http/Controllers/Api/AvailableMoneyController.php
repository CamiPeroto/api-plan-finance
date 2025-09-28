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
     * Lista entradas do mês atual com paginação
     */
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $availableMoneys = $this->objAvailableMoney
            ->where('user_id', Auth::id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date', 'desc')
            ->paginate(6);

        $availableMoneys->each(function ($entry) {
            $entry->formatted_date = Carbon::parse($entry->date)->format('d/m/Y');
        });

        return response()->json($availableMoneys, 200);
    }

    /**
     * Cria nova entrada
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'to_spend' => 'required|numeric',
            'date'     => 'required|date',
        ], [
            'name.required'     => 'O campo NOME é obrigatório!',
            'to_spend.required' => 'O campo VALOR é obrigatório!',
            'date.required'     => 'O campo DATA é obrigatório!',
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
     * Mostra uma entrada específica
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
     * Atualiza uma entrada existente
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
     * Deleta uma entrada
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
     * Busca entradas por nome ou valor
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
