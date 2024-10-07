<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    use HasFactory;

    // Agregar el campo `name` como fillable
    protected $fillable = ['attribute_id', 'name'];

    // Definir la relación con los atributos
    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }
}
