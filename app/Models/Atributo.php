<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    protected $fillable = ['atr_nombre'];

    public function valores()
    {
        return $this->hasMany(ValorAtributo::class);
    }
}
