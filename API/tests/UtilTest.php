<?php

namespace Test;

use App\Http\Controllers\UtilController;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Monolog\Formatter\JsonFormatter;

class UtilTest extends TestCase
{

    public function testAutenticacao()
    {
        $value = (object) [
            "id"=> 2,
            "nome"=> "Samuel Marques",
            "cpf"=> "06685136162",
            "email"=> "samuelmarques231198@gmail.com",
            "created_at"=> "2021-04-08 00:40:32",
            "updated_at"=> "2021-04-08 00:40:32",
        ];

        $this->assertEquals($value, UtilController::autenticar("06685136162", "123"));        
    }

    public function testVerificarUrl(){
        $this->assertEquals(true, UtilController::verificarURL("www.google.com"));
    }

}
