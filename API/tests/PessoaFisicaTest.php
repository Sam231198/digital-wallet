<?php

namespace Test;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PessoaFisicaTest extends TestCase
{

    public function testCadastroPF()
    {

        $this->post('/pf/cadastrar', [
            'nome' => 'Samuel Marques',
            'cpf' => '06685136162',
            'email' => 'samuelmarques231198@gmail.com',
            'saldo' => 1000.00,
            'senha' => '123',
        ]);

        $this->response->assertJsonFragment([
            'nome' => 'Samuel Marques',
            'cpf' => '06685136162',
            'email' => 'samuelmarques231198@gmail.com',
        ]);
    }

    public function testListagemPF()
    {
        $this->get('/pf/listar');

        $this->response->assertJsonFragment([
            'nome' => 'Samuel Marques',
            'cpf' => '06685136162',
            'email' => 'samuelmarques231198@gmail.com',
        ]);
    }
}
