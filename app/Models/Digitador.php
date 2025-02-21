<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Digitador extends Model
{
    use HasFactory;

    protected $table = 'digitadors';

    protected $fillable = ['name'];
}
