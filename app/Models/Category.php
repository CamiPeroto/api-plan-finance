<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'user_id',
        'name',
        'color',
    ];

    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relação com despesas
     */
    public function relSpentMoney()
    {
        return $this->hasMany(SpentMoney::class, 'categories_id');
    }

    /**
     * Relação com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
