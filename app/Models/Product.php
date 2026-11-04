<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'nama_menu',
        'harga_jual',
        'ingredient_id',
        'jumlah_potong',
    ];

    protected function casts(): array
    {
        return [
            'harga_jual' => 'decimal:2',
            'ingredient_id' => 'integer',
            'jumlah_potong' => 'integer',
        ];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
