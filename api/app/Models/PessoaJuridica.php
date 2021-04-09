<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PessoaJuridica extends Model
{
    protected $table = 'pessoa_juridica';

        
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
