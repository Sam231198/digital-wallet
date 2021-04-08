<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{

    protected $fillable = [
        "id",
        "user",
        "saldo",
        "tipo_conta",
        "created_at",
        "updated_at"
    ];

    protected $table = 'conta';
}
