<?php

namespace Test;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ContaTest extends TestCase
{

    public function testConsultaConta()
    {
        $this->post('/conta/consulta', [
            'cpf' => '06685136162',
            // 'cnpj' => '84814927000155',
            'senha' => '123'
        ]);

        $this->response->assertJsonFragment([
            'cpf' => '06685136162',
            "email"=>"samuelmarques231198@gmail.com",
            "nome"=>"Samuel Marques"

            // 'cnpj' => '84814927000155',
            // "email"=>"samuelmarques231198@outlook.com",
            // "nome"=>"Dev PHP"
        ]);
    }

    public function testTransferencia()
    {
        $this->post('/conta/transferencia', [
            'cpf' => '06685136162',
            'senha' => '123',
            'valor' => 10.00,
            'receptor' => '84814927000155'
        ]);

        $this->response->assertJsonFragment([
            'emissor' => '06685136162',
            'valor' => 10.00,
            'receptor' => '84814927000155'
        ]);
    }
}
