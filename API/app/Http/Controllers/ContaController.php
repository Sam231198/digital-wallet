<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Registro;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;
use phpDocumentor\Reflection\Types\Boolean;
use stdClass;

class ContaController extends Controller
{

    /**
     * 
     * Função de consulta de de saldo da conta
     * 
     * @return response
     * 
     */
    public function consultaConta(Request $request): Response
    {

        // verifica se todos os campos necessario foram enviados
        if ((!isset($request->cpf) || !isset($request->cnpj)) && !isset($request->senha))
            return response(json_encode(['message' => "Precisa ser passado os campos: cpf ou cnpj e senha."], 400));

        try {

            // autentica o usuario
            $autenticar = $this->autenticar($request);

            $user = (isset($request->cnpj)) ? $request->cnpj : $request->cpf;

            // retorna os dados do perfil conta e registros
            return response(json_encode(
                [
                    'perfil' => $autenticar,
                    'conta' => Conta::firstWhere('user', $user),
                    'registro' => DB::table('registro')->where(function ($query) use ($user) {
                        $query->where('emissor', $user)->orWhere('receptor', $user);
                    })->get()
                ],
                201
            ));
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
    public function transferencia(Request $request): Response
    {
        if (!isset($request->cpf) && !isset($request->senha))
            return response(json_encode(['message' => "Precisa ser passado os campos: cpf, senha, receptor (CPF ou CNPJ) e valor"], 400));

        try {

            // autenticando usuario 
            $autenticar = $this->autenticar($request);

            // buscando a conta do emissor e receptor
            $contaEmissor = Conta::firstWhere('user', $autenticar->cpf);
            $contaReceptor = Conta::firstWhere('user', $request->receptor);

            // verifica se a conta é do tipo 'PF' (Pessoa Física)
            if ($contaEmissor->tipo_conta != "PF")
                return response(json_encode(["message" => "Você não tem permissão para realizar transições"]), 401);

            // verifica se o cliente tem saldo suficiente para a tranferencia
            if ($contaEmissor->saldo <= $request->valor)
                return response(json_encode(["message" => "Você não tem saldo suficiente"]), 400);

            // consulta o muck externo de autorização
            $response_mock = file_get_contents('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
            $mock_autorizar = json_decode($response_mock);
            if ($mock_autorizar->message != "Autorizado")
                return response(json_encode(["message" => "transição não autorizada"]), 401);


            // mantem salvo os saldos antigos das contas antes da transferência para retorna o valor em caso de erro
            $saldoOld = new stdClass();
            $saldoOld->receptor = $contaReceptor->saldo;
            $saldoOld->emissor = $contaEmissor->saldo;

            // tenta realizar a transferencia 
            try {

                // verifica se o serviço externo está disponivel
                $confirmacaoEnvio = $this->verificarURL('https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04');
                if (!$confirmacaoEnvio)
                    return response(json_encode([
                        "message" => "Sistema externo não indisponivel", "variavel" => $confirmacaoEnvio
                    ]), 503);


                // realizando a troca de valores
                $contaReceptor->saldo = $contaReceptor->saldo + $request->valor;
                $contaEmissor->saldo = $contaEmissor->saldo - $request->valor;


                // vai tentar realizar a transição e vai verificar se foi transitato o valor
                if (!$contaEmissor->save() && !$contaReceptor->save())
                    return response(json_decode(["message" => $this->reestornoValor($contaEmissor, $contaReceptor, $saldoOld)]), 400);

                // confirmação do envio do valor 
                $mock_confirmacao = json_decode(file_get_contents('https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04'));
                if ($mock_confirmacao->message != "Enviado")
                    return response(json_encode(
                        [
                            "status" => $mock_confirmacao->message,
                            "message" => $this->reestornoValor($contaEmissor, $contaReceptor, $saldoOld),
                            "valor" => $request->valor
                        ]
                    ), 400);

                // faz o registro da transição
                $registro = Registro::create(
                    [
                        'emissor' => $request->cpf,
                        'receptor' => $request->receptor,
                        'valor' => $request->valor,
                        'descricao' => 'Transferência de dinheiro'
                    ]
                );

                return response(json_encode(
                    [
                        "registro" => $registro,
                        "status" => $mock_confirmacao->message,
                        "valor" => $request->valor
                    ]
                ), 200);
            } catch (\Throwable $th) {

                // caso ocorra um erro na transferencia os valores serão extornados 
                return response(json_encode(
                    [
                        "message" => $this->reestornoValor($contaEmissor, $contaReceptor, $saldoOld),
                        "erro" => $th->getMessage()
                    ]
                ), 500);
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
    protected function autenticar($request): Object
    {
        $banco = ($request->cpf) ? 'pessoa_fisica' : 'pessoa_juridica';
        $colum = ($request->cpf) ? 'cpf' : 'cnpj';
        $value = ($request->cpf) ? $request->cpf : $request->cnpj;

        $perfil = DB::table($banco)
            ->where([
                [$colum, '=', $value],
                ['senha', '=', md5($request->senha)]
            ])
            ->get();

        // verifica se retorna algun usuario
        if (empty($perfil[0]->id))
            throw new Exception('Erro de autenticação');

        unset($perfil[0]->senha);
        return $perfil[0];
    }


    /**
     * 
     * verifica se se uma url está ativa ou não
     * 
     * @return Boolean
     * 
     */
    protected function verificarURL($url): bool
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

    /**
     * 
     * função de reestorno do valor
     * 
     * @return String
     * 
     */
    private function reestornoValor($contaEmissor, $contaReceptor, $saldoOld): string
    {
        $contaEmissor->saldo = $saldoOld->emissor;
        $contaReceptor->saldo = $saldoOld->receptor;

        if (!$contaEmissor->save() && $contaReceptor->save())
            throw new Exception('Não foi possivel extorna o dinheiro');

        return "transição não efetuada, o dinheiro foi estornado";
    }
}
