<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    public const UNITS = ['potong', 'tusuk','porsi'];

    protected $fillable = [
        'nama_bahan',
        'satuan',
        'stok_saat_ini',
        'stok_minimum',
    ];

    protected function casts(): array
    {
        return [
            'stok_saat_ini' => 'integer',
            'stok_minimum' => 'integer',
        ];
    }
}
