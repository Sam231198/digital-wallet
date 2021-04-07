<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Registro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;

class ContaController extends Controller
{

    function consultaConta(Request $request)
    {
        try {
            $autenticar = $this->autenticar($request);

            if ($autenticar['status']) {
                return response(json_encode(['perfil' => $autenticar['perfil'], 'conta' => Conta::where('user', $autenticar['perfil']->cpf)->get()], 201));
            } else {
                return response(json_encode(['message' => "Erro de autenticação: CPF ou Senha incorretos"], 400));
            }
        } catch (\Throwable $th) {
            return response(json_encode(['message' => $th->getMessage()], 400));
        }
    }

    function transferencia(Request $request)
    {
        try {

            $autenticar = $this->autenticar($request);

            if ($autenticar['status'] && $autenticar['tipo'] === 'cliente') {

                $conta = Conta::firstWhere('user', $autenticar['perfil']->cpf);

                if ($conta->saldo >= $request->valor) {

                    $mock_autorizar = json_decode(file_get_contents('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6'));

                    if ($mock_autorizar->message === "Autorizado") {

                        $contaReceptor = Conta::firstWhere('user', $request->receptor);
                        $saldoOldReceptor = $contaReceptor->saldo;
                        $saldoOldEmissor = $conta->saldo;

                        try {

                            $contaReceptor->saldo = $contaReceptor->saldo + $request->valor;
                            $conta->saldo = $conta->saldo - $request->valor;

                            $conta->save();
                            $contaReceptor->save();

                            $registro = Registro::create([
                                'emissor' => $request->cpf,
                                'receptor' => $request->receptor,
                                'valor' => $request->valor,
                                'descricao' => 'Transferência de dinheiro'
                            ]);

                            return response(json_encode(["registro" => $registro]), 200);
                        } catch (\Throwable $th) {

                            $conta->saldo = $saldoOldEmissor;
                            $conta->save();

                            $contaReceptor->saldo = $saldoOldReceptor;
                            $contaReceptor->save();


                            return response(json_encode(["message" => "transição não efetuada, dinheiro foi estornado", "erro" => $th->getMessage()]), 400);
                        }
                    } else {
                        return response(json_encode(["message" => "transição não autorizada"]), 401);
                    }
                } else {
                    return response(json_encode(["message" => "Você não tem saldo suficiente"]), 400);
                }
            } else {
                return response(json_encode(["message" => "Você não tem permissão para realizar transições"]), 401);
            }
        } catch (\Throwable $th) {
            return response(json_encode(["message" => $th->getMessage()]), 500);
        }
    }

    function autenticar($request)
    {
        if ($request->cpf) $banco = 'cliente';
        elseif ($request->cnpj) $banco = 'empresa';

        $perfil = DB::table($banco)
            ->where([
                [($request->cpf) ? 'cpf' : 'cnpj', '=', ($request->cpf) ? $request->cpf : $request->cnpj],
                ['senha', '=', md5($request->senha)]
            ])
            ->get();

        if ($perfil[0]->id) {
            return ['perfil' => $perfil[0], 'tipo' => $banco, 'status' => true];
        } else {
            return ['status' => false];
        }
    }
}
