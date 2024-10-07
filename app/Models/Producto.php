<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'pro_nombre'
    ];

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
}
