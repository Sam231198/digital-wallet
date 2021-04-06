<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    
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
