<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registros extends Model
{
    protected $table = 'registros';

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
