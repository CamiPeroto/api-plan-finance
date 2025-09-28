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

    public function __construct()
    {
        $this->objSpentMoney = new SpentMoney();
        $this->objAvailableMoney = new AvailableMoney();
        $this->objCategory = new Category();
        $this->objPayment = new Payment();
    }

    /**
     * Retorna despesas do mês atual com paginação
     */
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $finances = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date', 'desc')
            ->paginate(6);

        $finances->each(function ($finance) {
            $finance->category = $finance->relCategory;
            $finance->payment = $finance->relPayment;
            $finance->formatted_date = Carbon::parse($finance->date)->format('d/m/Y');
        });

        $availableMoney = $this->objAvailableMoney
            ->where('user_id', Auth::id())
            ->get();

        $moneySpend = $availableMoney->sum('to_spend');
        $totalFinanceValue = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('value');
        $diff = $moneySpend - $totalFinanceValue;

        return response()->json([
            'finances' => $finances,
            'available_money' => $availableMoney,
            'diff' => $diff,
        ], 200);
    }

    /**
     * Cria nova despesa
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'value'              => 'required|numeric',
            'date'               => 'required|date',
            'available_money_id' => 'required|exists:available_money,id',
        ], [
            'name.required'               => 'O campo NOME é obrigatório!',
            'value.required'              => 'O campo VALOR é obrigatório!',
            'date.required'               => 'O campo DATA é obrigatório!',
            'available_money_id.required' => 'O SALDO não pode ser R$ 0,00',
        ]);

        $payable = $request->has('payable') ? 1 : 0;

        $finance = $this->objSpentMoney->create([
            'user_id'            => Auth::id(),
            'available_money_id' => $request->available_money_id,
            'categories_id'      => $request->categories_id,
            'payments_id'        => $request->payments_id ?? null,
            'name'               => $request->name,
            'description'        => $request->description,
            'payable'            => $payable,
            'value'              => $request->value,
            'date'               => $request->date,
        ]);

        return response()->json([
            'message' => 'Despesa adicionada com sucesso!',
            'finance' => $finance,
        ], 201);
    }

    /**
     * Mostra detalhes de uma despesa
     */
    public function show($id)
    {
        $finance = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->with(['relCategory', 'relPayment', 'relAvailableMoney'])
            ->findOrFail($id);

        $finance->formatted_date = Carbon::parse($finance->date)->format('d/m/Y');

        return response()->json($finance, 200);
    }

    /**
     * Atualiza despesa existente
     */
    public function update(Request $request, $id)
    {
        $finance = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'name'               => 'required|string|max:255',
            'value'              => 'required|numeric',
            'date'               => 'required|date',
            'available_money_id' => 'required|exists:available_money,id',
        ]);

        $finance->update([
            'name'               => $request->name,
            'description'        => $request->description,
            'value'              => $request->value,
            'date'               => $request->date,
            'categories_id'      => $request->categories_id,
            'payments_id'        => $request->payments_id ?? null,
            'payable'            => $request->has('payable') ? 1 : 0,
            'available_money_id' => $request->available_money_id,
        ]);

        return response()->json([
            'message' => 'Despesa atualizada com sucesso!',
            'finance' => $finance,
        ], 200);
    }

    /**
     * Deleta uma despesa
     */
    public function destroy($id)
    {
        $finance = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $finance->delete();

        return response()->json([
            'message' => 'Despesa deletada com sucesso!',
        ], 200);
    }

    /**
     * Busca despesas por nome, categoria ou valor
     */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $finances = $this->objSpentMoney
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhereHas('relCategory', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhere('value', 'like', '%' . $search . '%');
            })
            ->paginate(5);

        return response()->json($finances, 200);
    }
}
