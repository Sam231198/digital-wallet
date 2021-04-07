<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use App\Models\Contas;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class UsuariosController extends Controller
{


    /**
     * 
     * Função que retorna um autoload em json das informações do Usuarios
     * 
     * @return response
     * 
     */
    public function show()
    {
        return response(Usuarios::all(), 200);
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
            $usuario = Usuarios::create([
                "nome" => $request->nome,
                "codigo_pessoa" => $request->codigo_pessoa,
                "tipo" => (strlen($request->codigo_pessoa) > 11)?'PJ':'PF',
                "email" => $request->email,
                "senha" => md5($request->senha),
            ]);

            $conta = Contas::create([
                "user" => $usuario->codigo_pessoa,
                "saldo" => ($request->saldo) ? $request->saldo : 0.00,
            ]);

            return response(json_encode(["perfil" => $usuario, "conta" => $conta]), 201);
        } catch (\Throwable $th) {
            if ($usuario)
                return response(json_encode(["message" => $th->getMessage()]), 500);
        }
    }
}
