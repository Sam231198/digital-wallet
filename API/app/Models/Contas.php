<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contas extends Model
{
    protected $table = 'contas';

    protected $fillable = [
        "id",
        "user",
        "saldo",
        "created_at",
        "updated_at"
    ];

}
