<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Laravel\Lumen\Routing\Controller;

class ContaController extends Controller
{

    public function show()
    {
        return response(Conta::all(), 200);
    }

}
