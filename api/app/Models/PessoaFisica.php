<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PessoaFisica extends Model
{
    protected $table = 'pessoa_fisica';
    
    protected $fillable = [
        "id",
        "nome",
        "cpf",
        "email",
        "senha",
        "created_at",
        "updated_at"
    ];
}
