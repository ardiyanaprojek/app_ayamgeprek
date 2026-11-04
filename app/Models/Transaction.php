<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'nomor_struk',
        'total_bayar',
        'nominal_diterima',
        'kembalian',
        'metode_bayar',
    ];

    protected function casts(): array
    {
        return [
            'total_bayar' => 'decimal:2',
            'nominal_diterima' => 'decimal:2',
            'kembalian' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}
