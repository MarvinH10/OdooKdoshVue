<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    use HasFactory;

    protected $fillable = ['sub_nombre', 'cat_id'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
