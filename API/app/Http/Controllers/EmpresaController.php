<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Conta;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class EmpresaController extends Controller
{

    public function show()
    {
        return response(Empresa::all(), 200);
    }

    public function create(Request $request)
    {
        $empresa = 0;

        try {
            $empresa = Empresa::create([
                "nome" => $request->nome,
                "cnpj" => $request->cnpj,
                "email" => $request->email,
                "senha" => md5($request->senha),
            ]);
            // $empresa = Empresa::saved();

            $conta = Conta::create([
                "user" => $empresa->cnpj,
                "tipo_conta" => "Pessoa Jurídica",
            ]);

            return response(json_encode(["perfil" => $empresa, "conta" => $conta]), 201);
        } catch (\Throwable $th) {
            if ($empresa)
                return response(json_encode(["message" => $th->getMessage()]), 500);
        }
    }
}
