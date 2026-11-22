<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    protected $fillable = [
        'nama_warung',
        'alamat',
        'telepon',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'nama_warung' => 'AYAM GEPREK ISTIQOMAH',
            'alamat' => 'PADAKASIH',
            'telepon' => null,
        ]);
    }
}
