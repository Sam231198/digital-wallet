<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


// exibe a versão do lumen e do php 
$router->get('/', function () use ($router) {
    return $router->app->version();
});


// mapeamento de rotas para a funcionalidade do cliente
$router->group(['prefix' => 'usuarios'], function () use ($router) {

    // lista todos os usuario 
    $router->get('/listar', ['as' => 'lista-cliente', 'uses' => 'UsuariosController@show']);
    
    // cadastra um usuario 
    $router->post('/cadastrar', ['as' => 'cadastrar-cliente', 'uses' => 'UsuariosController@create']);

});

// mapeamento de rotas para a funcionalidade da conta
$router->group(['prefix' => 'conta'], function () use ($router) {

    // consulta conta
    $router->post('/consulta', ['as' => 'consultar-conta', 'uses' => 'ContasController@consultaConta']);
    
    // realizar tranferencia
    $router->post('/transferencia', ['as' => 'transferencia-conta', 'uses' => 'ContasController@transferencia']);
});
   