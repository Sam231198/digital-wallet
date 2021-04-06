<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Conta;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ClienteController extends Controller
{


    /**
     * 
     * Função que retorna um autoload em json das informações do cliente
     * 
     * @return response
     * 
     */
    public function show()
    {
        return response(Cliente::all(), 200);
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
        $cliente = 0;

        try {
            $cliente = Cliente::create([
                "nome" => $request->nome,
                "cpf" => $request->cpf,
                "email" => $request->email,
                "senha" => md5($request->senha),
            ]);

            $conta = Conta::create([
                "user" => $cliente->cpf,
                "tipo_conta" => "Pessoa Física",
            ]);

            return response(json_encode(["cliente" => $cliente, "conta" => $conta]), 201);
        } catch (\Throwable $th) {
            if ($cliente)
                return response(json_encode(["messenger" => $th->getMessage()]), 500);
        }
    }


    function createConta(Request $request)
    {
        $cliente = Cliente::where('cpf', $request->cpf)
            ->where('cpf', $request->cpf)
            ->get();
        
        if($cliente){
            return 
        }
    }
}
