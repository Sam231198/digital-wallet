<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';

        
    protected $fillable = [
        "id",
        "nome",
        "cnpj",
        "email",
        "senha",
        "created_at",
        "updated_at"
    ];
}
