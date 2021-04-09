<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;
use phpDocumentor\Reflection\Types\Boolean;


class UtilController extends Controller
{

    /**
     * 
     * Função de autenticação do usuario
     * 
     * @param string $identificador recebe o CPF ou CNPJ
     * @param string $senha recebe a senha do usuário
     * 
     * @return Object
     * 
     */
    public static function autenticar(string $identificador, string $senha): Object
    {
        $banco = (strlen($identificador) === 11) ? 'pessoa_fisica' : 'pessoa_juridica';
        $colum = (strlen($identificador) === 11) ? 'cpf' : 'cnpj';

        $perfil = DB::table($banco)
            ->where([
                [$colum, '=', $identificador],
                ['senha', '=', md5($senha)]
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
     * @param string $url recebe a URL que será testada
     * 
     * @return Boolean
     * 
     */
    public static function verificarURL(string $url): bool
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
