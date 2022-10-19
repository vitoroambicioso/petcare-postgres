<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Denuncia extends Model
{
    use HasApiTokens, HasFactory;

   /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idUsuario',
        'tipo',
        'cor',
        'localizacao',
        'rua',
        'bairro',
        'pontoDeReferencia',
        'picture'
    ];
}
