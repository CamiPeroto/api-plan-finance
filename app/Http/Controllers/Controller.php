<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="APIs For Thrift Store",
 *     version="1.0.0"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Schema(
 *     schema="AvailableMoney",
 *     type="object",
 *     title="AvailableMoney",
 *     required={"id","user_id","name","to_spend","date"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Saldo inicial"),
 *     @OA\Property(property="to_spend", type="number", format="float", example=1000.50),
 *     @OA\Property(property="date", type="string", format="date", example="2025-09-28"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-28T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-28T10:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Payment",
 *     type="object",
 *     title="Payment",
 *     required={"id","name"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Cartão de crédito"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-28T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-28T10:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     required={"id","user_id","name"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Alimentação"),
 *     @OA\Property(property="color", type="string", example="#FF0000"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-28T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-28T10:00:00Z")
 * )
 * * @OA\Schema(
 *     schema="Finance",
 *     type="object",
 *     title="Despesa",
 *     required={"id","name","value","date","available_money_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Compra supermercado"),
 *     @OA\Property(property="description", type="string", example="Compra semanal de alimentos"),
 *     @OA\Property(property="value", type="number", format="float", example=150.75),
 *     @OA\Property(property="date", type="string", format="date", example="2025-09-28"),
 *     @OA\Property(property="payable", type="boolean", example=false),
 *     @OA\Property(property="available_money_id", type="integer", example=1),
 *     @OA\Property(property="categories_id", type="integer", example=2),
 *     @OA\Property(property="payments_id", type="integer", example=1),
 *     @OA\Property(
 *         property="category",
 *         ref="#/components/schemas/Category"
 *     ),
 *     @OA\Property(
 *         property="payment",
 *         ref="#/components/schemas/Payment"
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-28T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-28T10:00:00Z")
 * )
 */


abstract class Controller
{
    //
}
