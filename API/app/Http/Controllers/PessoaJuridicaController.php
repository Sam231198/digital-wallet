<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\PessoaJuridica;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;

class PessoaJuridicaController extends Controller
{

    /**
     * 
     * Função que retorna um autoload em json das informações do Pessoa Jurídica
     * 
     * @return response
     * 
     */
    public function show(): Response
    {
        $result = PessoaJuridica::all();
        if ($result[0])
            foreach ($result as $value) {
                unset($value->senha);
            }
        return response($result, 200);
    }

    /**
     * 
     * Função de cadastro de Pessoas Jurídica
     * 
     * @param Response $response recebe os parametros passado na requisição
     * 
     * @return Response
     * 
     */
    public function create(Request $request): Response
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
