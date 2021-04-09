<?php

namespace Test;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PessoaJuridicaTest extends TestCase
{

    public function testCadastroPJ()
    {

        $this->post('/pj/cadastrar', [
            'nome' => 'Dev PHP',
            'cnpj' => '84814927000155',
            'email' => 'samuelmarques231198@outlook.com',
            'senha' => '123'
        ]);

        $this->response->assertJsonFragment([
            'nome' => 'Dev PHP',
            'cnpj' => '84814927000155',
            'email' => 'samuelmarques231198@outlook.com'
        ]);
    }

    public function testListagemPJ()
    {
        $this->get('/pj/listar');

        $this->response->assertJsonFragment([
            'nome' => 'Dev PHP',
            'cnpj' => '84814927000155',
            'email' => 'samuelmarques231198@outlook.com'
        ]);
    }

}
