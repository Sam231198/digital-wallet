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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'cliente'], function () use ($router) {

    // lista todos os usuario 
    $router->get('/lista', ['as' => 'lista-cliente', 'uses' => 'ClienteController@show']);
    
    // cadastra um usuario 
    $router->post('/cadastrar', ['as' => 'cadastrar-cliente', 'uses' => 'ClienteController@create']);

});

$router->group(['prefix' => 'empresa'], function () use ($router) {

    // lista todos os usuario 
    $router->get('/lista', ['as' => 'lista-empresa', 'uses' => 'EmpresaController@show']);
    
    // cadastra um usuario 
    $router->post('/cadastrar', ['as' => 'cadastrar-empresa', 'uses' => 'EmpresaController@create']);

});
