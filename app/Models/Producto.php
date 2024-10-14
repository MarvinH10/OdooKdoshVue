<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'name', 'default_code', 'categ_id', 'subcateg1_id', 'subcateg2_id', 'subcateg3_id', 'subcateg4_id', 'list_price', 'attributes',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];
}
