<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\PessoaFisica;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class PessoaFisicaController extends Controller
{


    /**
     * 
     * Função que retorna um autoload em json das informações do PessoaFisica
     * 
     * @return response
     * 
     */
    public function show()
    {
        $result = PessoaFisica::all();
        foreach ($result as $value) {
            unset($value->senha);
        }
        return response($result, 200);
    }

    /**
     * 
     * função de cadastro de clentes
     * 
     * exemplo json:
     * 
     * {
     *     'nome' => 'Nome Completo',
     *     'cpf' => '00000000000',
     *     'email' => 'email@email.com',
     *     'senha' => 'secret'
     * }
     * 
     * @return response
     * 
     */
    public function create(Request $request)
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
