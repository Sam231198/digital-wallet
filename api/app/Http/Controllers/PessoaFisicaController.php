<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\PessoaFisica;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;

class PessoaFisicaController extends Controller
{

    /**
     * 
     * Função que retorna um autoload em json das informações do Pessoa Física
     * 
     * @return response
     * 
     */
    public function show(): Response
    {
        $result = PessoaFisica::all();
        if ($result[0])
            foreach ($result as $value) {
                unset($value->senha);
            }
        return response($result, 200);
    }

    /**
     * 
     * Função de cadastro de Pessoas Físicas
     * 
     * @param Response $response recebe os parametros passado na requisição
     * 
     * @return Response
     * 
     */
    public function create(Request $request): Response
    {

        try {
            $pessoaFisica = PessoaFisica::create([
                "nome" => $request->nome,
                "cpf" => $request->cpf,
                "email" => $request->email,
                "senha" => md5($request->senha),
            ]);

            $conta = Conta::create([
                "user" => $pessoaFisica->cpf,
                "saldo" => ($request->saldo) ? $request->saldo : 0.00,
                "tipo_conta" => "PF",
            ]);

            return response(json_encode(["perfil" => $pessoaFisica, "conta" => $conta]), 201);
        } catch (\Throwable $th) {
            if (!empty($pessoaFisica))
                return response(json_encode(["message" => $th->getMessage()]), 500);
        }
    }
}
