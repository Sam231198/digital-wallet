<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;
use PHPUnit\Framework\MockObject\Stub\ReturnSelf;

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
                "saldo" => ($request->saldo) ? $request->saldo : 0.00,
                "tipo_conta" => "Pessoa Física",
            ]);

            return response(json_encode(["perfil" => $cliente, "conta" => $conta]), 201);
        } catch (\Throwable $th) {
            if ($cliente)
                return response(json_encode(["message" => $th->getMessage()]), 500);
        }
    }
}
