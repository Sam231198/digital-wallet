<?php

namespace Test;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UsuariosTest extends TestCase
{

    public function testCadastroUsuario()
    {

        $this->post('/usuarios/cadastrar', [
            'nome' => 'Samuel Marques',
            'codigo_pessoa' => '06685136162',
            'email' => 'samuelmarques231198@gmail.com',
            'saldo' => 1000.00,
            'senha' => '123',
        ]);

        $this->response->assertJsonFragment([
            'nome' => 'Samuel Marques',
            'codigo_pessoa' => '06685136162',
            'email' => 'samuelmarques231198@gmail.com',
            'tipo' => 'PF',
            'saldo' => 1000.00,
        ]);
    }

    public function testListagemUsuario()
    {
        $this->get('/usuarios/listar');

        $this->response->assertJsonFragment([
            'nome' => 'Samuel Marques',
            'codigo_pessoa' => '06685136162',
            'email' => 'samuelmarques231198@gmail.com',
            'saldo' => 1000.00,
        ]);
    }
}
