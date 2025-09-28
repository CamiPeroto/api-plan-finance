<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'name',
    ];

    /**
     * Relação com despesas (SpentMoney)
     */
    public function relSpentMoney()
    {
        return $this->hasMany(SpentMoney::class, 'payments_id');
    }

    /**
     * Casts opcionais para API (se quiser forçar tipos)
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
    ];
}
