<?php
namespace Test;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class EmpresaTest extends TestCase
{

    public function testCadastroEmpresa()
    {

        $this->post('/empresa/cadastrar', [
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

    public function testListagemEmpresa()
    {
        $this->get('/empresa/listar');

        $this->response->assertJsonFragment([
            'nome' => 'Dev PHP',
            'cnpj' => '84814927000155',
            'email' => 'samuelmarques231198@outlook.com'
        ]);
    }
}
