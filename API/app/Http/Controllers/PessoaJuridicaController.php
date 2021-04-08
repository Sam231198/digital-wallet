<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\PessoaJuridica;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class PessoaJuridicaController extends Controller
{

    public function show()
    {
        $result = PessoaJuridica::all();
        foreach ($result as $value) {
            unset($value->senha);
        }
        return response($result, 200);
    }

    public function create(Request $request)
    {
        $pessoaJuridica = 0;

        try {
            $pessoaJuridica = PessoaJuridica::create([
                "nome" => $request->nome,
                "cnpj" => $request->cnpj,
                "email" => $request->email,
                "senha" => md5($request->senha),
            ]);
            // $pessoaJuridica = PessoaJuridica::saved();

            $conta = Conta::create([
                "user" => $pessoaJuridica->cnpj,
                "tipo_conta" => "PJ",
            ]);

            return response(json_encode(["perfil" => $pessoaJuridica, "conta" => $conta]), 201);
        } catch (\Throwable $th) {
            if ($pessoaJuridica)
                return response(json_encode(["message" => $th->getMessage()]), 500);
        }
    }
}
