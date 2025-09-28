<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    private $objCategory;

    public function __construct()
    {
        $this->objCategory = new Category();
    }

    public function index()
    {
        $categories = $this->objCategory
            ->where('user_id', Auth::id())
            ->paginate(6);

        return response()->json($categories, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        $category = $this->objCategory->create([
            'user_id' => Auth::id(),
            'name'    => $request->name,
            'color'   => $request->color,
        ]);

        return response()->json([
            'message'  => 'Categoria adicionada com sucesso!',
            'category' => $category,
        ], 201);
    }

    public function show(string $id)
    {
        $category = $this->objCategory
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json($category, 200);
    }

    public function update(Request $request, string $id)
    {
        $category = $this->objCategory
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        $category->update([
            'name'  => $request->name,
            'color' => $request->color,
        ]);

        return response()->json([
            'message'  => 'Categoria atualizada com sucesso!',
            'category' => $category,
        ], 200);
    }

    public function destroy(string $id)
    {
        $category = $this->objCategory
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $category->delete();

        return response()->json([
            'message' => 'Categoria deletada com sucesso!',
        ], 200);
    }

    public function search(Request $request)
    {
        $categories = $this->objCategory
            ->where('user_id', Auth::id())
            ->where('name', 'like', '%' . $request->search . '%')
            ->paginate(5);

        return response()->json($categories, 200);
    }
}
