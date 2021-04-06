<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Conta;
use Illuminate\Http\Request;

class ClienteController extends Controller
{

    public function show()
    {
        return Cliente::all();
    }

    public function create(Request $request)
    {
        $cliente = 0;

        try {
            $cliente = Cliente::create([
                "nome" => $request->nome,
                "cpf" => $request->cpf,
                "email" => $request->email,
                "senha" => md5($request->senha),
            ]);
            // $cliente = Cliente::saved();

            Conta::create([
                "id_user" => $cliente->id,
                "tipo_conta" => "Pessoa Física",
            ]);

            return response(json_encode(["messenger" => "cadastrado"]), 201);
        } catch (\Throwable $th) {
            if ($cliente)


                return response(json_encode(["messenger" => $th->getMessage()]), 400);
        }
    }
}
