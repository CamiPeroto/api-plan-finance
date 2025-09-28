<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpentMoney extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'spent_money';

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'date'];

    protected $fillable = [
        'user_id',
        'available_money_id',
        'categories_id',
        'payments_id',
        'name',
        'description',
        'value',
        'payable',
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Garante que o valor seja armazenado como double
     */
    public function setValueAttribute($value)
    {
        $cleanValue = preg_replace('/[^\d.]/', '', str_replace(',', '.', $value));
        $this->attributes['value'] = (double) $cleanValue;
    }

    /**
     * Relação com o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação com categoria
     */
    public function relCategory()
    {
        return $this->hasOne(Category::class, 'id', 'categories_id');
    }

    /**
     * Relação com pagamento
     */
    public function relPayment()
    {
        return $this->hasOne(Payment::class, 'id', 'payments_id');
    }

    /**
     * Relação com saldo disponível
     */
    public function relAvailableMoney()
    {
        return $this->hasOne(AvailableMoney::class, 'id', 'available_money_id');
    }

    /**
     * Casts para API
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'available_money_id' => 'integer',
        'categories_id' => 'integer',
        'payments_id' => 'integer',
        'value' => 'double',
        'payable' => 'boolean',
        'date' => 'datetime:Y-m-d',
    ];
}
