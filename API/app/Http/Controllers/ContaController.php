<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Registro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;

class ContaController extends Controller
{

    /**
     * 
     * Função de consulta de de saldo da conta
     * 
     * Request:
     * {
     *  "cpf"
     * }
     * 
     * @return response
     * 
     */
    function consultaConta(Request $request)
    {
        try {

            // autentica o usuario
            $autenticar = $this->autenticar($request);

            // verifica o statos da altenticação: true || false
            if ($autenticar['status']) {

                $user = (isset($request->cnpj)) ? $request->cnpj : $request->cpf;

                // retorna os dados do perfil conta e registros
                return response(json_encode([
                    'perfil' => $autenticar['perfil'],
                    'conta' => Conta::where('user', $user)->get(),
                    'registro' => DB::table('registro')->where(function ($query) use ($user) {
                        $query->where('emissor', $user)->orWhere('receptor', $user);
                    })->get()
                ], 201));
            } else {
                return response(json_encode(['message' => "Erro de autenticação: CPF/CNPJ ou Senha incorretos"], 400));
            }
        } catch (\Throwable $th) {
            return response(json_encode(['message' => $th->getMessage()], 400));
        }
    }


    /**
     * 
     * Função de realização de transferencia
     * 
     * @return response
     * 
     */
    function transferencia(Request $request)
    {
        try {

            // autenticando usuario 
            $autenticar = $this->autenticar($request);

            // verifica o estatus da autenticação e se a conta é do tipo 'cliente'
            if ($autenticar['status'] && $autenticar['tipo'] === 'cliente') {

                $conta = Conta::firstWhere('user', $autenticar['perfil']->cpf);

                // verifica se o cliente tem saldo suficiente para a tranferencia
                if ($conta->saldo >= $request->valor) {

                    // consulta o muck externo de autorização 
                    $mock_autorizar = json_decode(file_get_contents('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6'));
                    if ($mock_autorizar->message === "Autorizado") {

                        $contaReceptor = Conta::firstWhere('user', $request->receptor);

                        // mantei salvo os saldos das contas antes da transferencia para retorna o valor em caso de erro
                        $saldoOldReceptor = $contaReceptor->saldo;
                        $saldoOldEmissor = $conta->saldo;

                        // tenta realizar a transferencia 
                        try {

                            $contaReceptor->saldo = $contaReceptor->saldo + $request->valor;
                            $conta->saldo = $conta->saldo - $request->valor;

                            // verifica se o serviço externo está disponivel 
                            if (checkdnsrr('https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04')) {

                                if ($conta->save() && $contaReceptor->save()) {
                                    $registro = Registro::create([
                                        'emissor' => $request->cpf,
                                        'receptor' => $request->receptor,
                                        'valor' => $request->valor,
                                        'descricao' => 'Transferência de dinheiro'
                                    ]);

                                    $mock_autorizar = json_decode(file_get_contents('https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04'));
                                    if ($mock_autorizar->message === "Enviado") {
                                        return response(json_encode(["registro" => $registro, $mock_autorizar]), 200);
                                    } else {
                                        // caso ocorra um erro na transferencia os valores serão extornados 
                                        $conta->saldo = $saldoOldEmissor;
                                        $conta->save();

                                        $contaReceptor->saldo = $saldoOldReceptor;
                                        $contaReceptor->save();

                                        return response(json_encode([$mock_autorizar]), 400);
                                    }
                                } else {
                                    return response($mock_autorizar, 400);
                                }
                            } else {
                                return response(json_encode(["message" => "Sistema externo não indisponivel"]), 503);
                            }
                        } catch (\Throwable $th) {

                            // caso ocorra um erro na transferencia os valores serão extornados 
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


    /**
     * 
     * Função de autenticação do usuario
     * 
     * @return Array
     * 
     */
    function autenticar($request)
    {
        if ($request->cpf) $banco = 'cliente';
        elseif ($request->cnpj) $banco = 'empresa';

        $perfil = DB::table($banco)
            ->where([
                [($request->cpf) ? 'cpf' : 'cnpj', '=', ($request->cpf) ? $request->cpf : $request->cnpj],
                ['senha', '=', md5($request->senha)]
            ])
            ->get()[0];

        unset($perfil->senha);

        if ($perfil->id) {
            return ['perfil' => $perfil, 'tipo' => $banco, 'status' => true];
        } else {
            return ['status' => false];
        }
    }
}
