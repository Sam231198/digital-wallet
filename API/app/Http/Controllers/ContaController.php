<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Registro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;
use stdClass;

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
            if ($autenticar->status) {

                $user = (isset($request->cnpj)) ? $request->cnpj : $request->cpf;

                // retorna os dados do perfil conta e registros
                return response(json_encode([
                    'perfil' => $autenticar->perfil,
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

            // buscando a conta do emissor 
            $contaEmissor = Conta::firstWhere('user', $autenticar->perfil->cpf);

            // verifica o estatus da autenticação e se a conta é do tipo 'PF' (Pessoa Física)
            if ($autenticar->status && $contaEmissor->tipo_conta == "PF") {
                $contaReceptor = Conta::firstWhere('user', $request->receptor);

                // verifica se o cliente tem saldo suficiente para a tranferencia
                if ($contaEmissor->saldo >= $request->valor) {

                    // consulta o muck externo de autorização
                    $rsponse_mock = file_get_contents('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
                    $mock_autorizar = json_decode($rsponse_mock);
                    if ($mock_autorizar->message === "Autorizado") {


                        // mantei salvo os saldos das contas antes da transferencia para retorna o valor em caso de erro
                        $saldoOld = new stdClass();
                        $saldoOld->receptor = $contaReceptor->saldo;
                        $saldoOld->emissor = $contaEmissor->saldo;

                        // tenta realizar a transferencia 
                        try {

                            $contaReceptor->saldo = $contaReceptor->saldo + $request->valor;
                            $contaEmissor->saldo = $contaEmissor->saldo - $request->valor;

                            // verifica se o serviço externo está disponivel
                            $confirmacaoEnvio = $this->verificarURL('https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04');
                            if ($confirmacaoEnvio) {

                                if ($contaEmissor->save() && $contaReceptor->save()) {
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
                                        $contaEmissor->saldo = $saldoOld->emissor;
                                        $contaEmissor->save();
                                        $contaReceptor->saldo = $saldoOld->receptor;
                                        $contaReceptor->save();

                                        return response(json_encode([$mock_autorizar, "extorno" => $request->valor]), 400);
                                    }
                                } else {
                                    return response($mock_autorizar, 400);
                                }
                            } else {
                                return response(json_encode([
                                    "message" => "Sistema externo não indisponivel", "variavel" => $confirmacaoEnvio
                                ]), 503);
                            }
                        } catch (\Throwable $th) {

                            // caso ocorra um erro na transferencia os valores serão extornados 
                            $contaEmissor->saldo = $saldoOld->emissor;
                            $contaEmissor->save();

                            $contaReceptor->saldo = $saldoOld->receptor;
                            $contaReceptor->save();

                            return response(json_encode([
                                "message" => "transição não efetuada,
                                dinheiro foi estornado", "erro" => $th->getMessage()
                            ]), 400);
                        }
                    } else {
                        return response(json_encode(["message" => "transição não autorizada"]), 401);
                    }
                } else {
                    return response(json_encode(["message" => "Você não tem saldo suficiente"]), 400);
                }
            } else {
                return response(
                    json_encode([
                        "message" => "Você não tem permissão para realizar transições"
                    ]),
                    401
                );
            }
        } catch (\Throwable $th) {
            return response(json_encode(["message" => $th->getMessage()]), 500);
        }
    }


    /**
     * 
     * Função de autenticação do usuario
     * 
     * @return Object
     * 
     */
    protected function autenticar($request)
    {
        $banco = ($request->cpf) ? 'pessoa_fisica' : 'pessoa_juridica';
        $colum = ($request->cpf) ? 'cpf' : 'cnpj';
        $value = ($request->cpf) ? $request->cpf : $request->cnpj;

        $perfil = DB::table($banco)
            ->where([
                [$colum, '=', $value],
                ['senha', '=', md5($request->senha)]
            ])
            ->get()[0];

        unset($perfil->senha);

        if ($perfil->id) {
            $array = ['perfil' => $perfil, 'status' => true];
            return (object) $array;
        } else {
            $array = ['status' => false];
            return (object) $array;
        }
    }


    protected function verificarURL($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);             // Inicia uma nova sessão do cURL
        curl_setopt($curl, CURLOPT_NOBODY, true);          // Define que iremos realizar uma requisição "HEAD"
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false); // Não exibir a saída no navegador
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Não verificar o certificado do site

        curl_exec($curl);  // Executa a sessão do cURL
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE) === 200; // Se a resposta for OK, a URL está ativa
        curl_close($curl); // Fecha a sessão do cURL

        return $status;
    }
}
