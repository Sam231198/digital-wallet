<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    protected $table = 'registro';

    protected $fillable = [
        "id",
        "emissor",
        "receptor",
        "valor",
        "descricao",
        "created_at",
        "updated_at"
    ];
}
