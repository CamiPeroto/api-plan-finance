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

    /**
     * @OA\Get(
     *     path="/api/categoria",
     *     summary="Lista categorias do usuário",
     *     tags={"Category"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $categories = $this->objCategory
            ->where('user_id', Auth::id())
            ->paginate(6);

        return response()->json($categories, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/categoria",
     *     summary="Cria nova categoria",
     *     tags={"Category"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Transporte"),
     *             @OA\Property(property="color", type="string", example="#00FF00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoria criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/categoria/{id}",
     *     summary="Mostra detalhes de uma categoria",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da categoria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoria encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoria não encontrada"
     *     )
     * )
     */
    public function show(string $id)
    {
        $category = $this->objCategory
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json($category, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/categoria/{id}",
     *     summary="Atualiza uma categoria",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da categoria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Transporte Atualizado"),
     *             @OA\Property(property="color", type="string", example="#0000FF")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoria atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/categoria/{id}",
     *     summary="Deleta uma categoria",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da categoria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoria deletada com sucesso"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/categoria/search",
     *     summary="Busca categorias por nome",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Termo de busca",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorias filtradas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        $categories = $this->objCategory
            ->where('user_id', Auth::id())
            ->where('name', 'like', '%' . $request->search . '%')
            ->paginate(5);

        return response()->json($categories, 200);
    }
}
