<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalClosing extends Model
{
    protected $fillable = [
        'closing_date',
        'total_sales',
        'transaction_count',
        'cash_counted',
        'cash_difference',
        'stock_snapshot',
        'notes',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'closing_date' => 'date',
            'total_sales' => 'decimal:2',
            'transaction_count' => 'integer',
            'cash_counted' => 'decimal:2',
            'cash_difference' => 'decimal:2',
            'stock_snapshot' => 'array',
            'closed_at' => 'datetime',
        ];
    }
}
