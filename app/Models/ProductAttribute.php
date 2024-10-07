<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id', 'attribute_id', 'value_ids', 'extra_references', 'extra_prices',
    ];

    protected $casts = [
        'value_ids' => 'array',
        'extra_references' => 'array',
        'extra_prices' => 'array',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
