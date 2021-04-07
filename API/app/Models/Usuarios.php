<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    protected $table = 'usuarios';
    
    protected $fillable = [
        "id",
        "nome",
        "codigo_pessoa",
        "email",
        "senha",
        "tipo",
        "created_at",
        "updated_at"
    ];
}
