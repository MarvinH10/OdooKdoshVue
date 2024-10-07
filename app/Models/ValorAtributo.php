<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValorAtributo extends Model
{
    use HasFactory;

    protected $fillable = ['atr_id', 'val_nombre'];

    public function atributo()
    {
        return $this->belongsTo(Atributo::class);
    }
}
