<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvailableMoney extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'available_money'; // confirme se sua migration usou esse nome

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'user_id',
        'name',
        'to_spend',
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Formata e garante que o valor "to_spend" seja salvo como double
     */
    public function setToSpendAttribute($value)
    {
        $cleanValue = preg_replace('/[^\d.]/', '', str_replace(',', '.', $value));
        $this->attributes['to_spend'] = (double) $cleanValue;
    }

    /**
     * Relação com os gastos (SpentMoney)
     */
    public function spentMoney()
    {
        return $this->hasMany(SpentMoney::class, 'available_money_id', 'id');
    }

    /**
     * Relação com o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Casts para API
     */
    protected $casts = [
        'date'      => 'datetime:Y-m-d',
        'to_spend'  => 'double',
    ];
}
