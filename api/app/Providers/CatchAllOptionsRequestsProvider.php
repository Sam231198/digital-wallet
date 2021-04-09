<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 *  Se a solicitação de entrada for uma solicitação OPTIONS, registraremos um manipulador para a rota solicitada
 */
class CatchAllOptionsRequestsProvider extends ServiceProvider {

  public function register()
  {
    $request = app('request');

    if ($request->isMethod('OPTIONS'))
    {
      app()->options($request->path(), function() { return response('', 200); });
    }
  }

}
