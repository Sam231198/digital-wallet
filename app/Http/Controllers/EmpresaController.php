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
        return Empresa::all();
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

            Conta::create([
                "id_user" => $empresa->id,
                "tipo_conta" => "Pessoa Jurídica",
            ]);

            return response(json_encode(["messenger" => "cadastrado"]), 201);
        } catch (\Throwable $th) {
            if ($empresa)


                return response(json_encode(["messenger" => $th->getMessage()]), 400);
        }
    }
}
