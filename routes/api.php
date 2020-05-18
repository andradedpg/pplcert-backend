<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'cors'], function () {
    Route::get('/check', ['uses' => 'UsuarioController@checkToken']);
    Route::post('/mail/send/{layout}', ['uses' => 'MailController@send']);

    Route::group(['middleware' => 'auth:api'], function () {
        // Route::get('/menus/getByModuloLogin/{id}', ['uses' => 'MenuController@getMenusModuloByLogin']);
        
        // Route::resource('eventos', 'EventoController');
        // Route::get('/eventos/search/disponiveis', ['uses' => 'EventoController@getEventosDisponiveis']);
        // Route::get('/eventos/configuracao/getConfigLampadas/{evento_id}', ['uses' => 'EventoController@getConfigDoacaoLampada']);
        // Route::get('/eventos/configuracao/getTiposLampada', ['uses' => 'EventoController@getTiposLampadas']);
        // Route::post('/eventos/doacao/sucata', ['uses' => 'EventoController@doarSucata']);
        
        // Route::resource('users', 'UsuarioController');
        // Route::get('/user/getUserInfo', ['uses' => 'UsuarioController@getUser']);
        // Route::post('/logout', ['uses' => 'UsuarioController@logout']);

        // Route::resource('contratos', 'ContratoController');
        // Route::post('/contratos/searchBy', ['uses' => 'ContratoController@searchByNomeOrNumero']);
        // Route::post('/contratos/vincularCliente', ['uses' => 'ContratoController@setCliente']);


        // Route::resource('cec', 'ClienteEventoContratosController');
        // Route::post('/cec/searchBy', ['uses' => 'ClienteEventoContratosController@searchByNomeOrNumero']);
        // Route::get('/cec/indexByEvento/{id}', ['uses' => 'ClienteEventoContratosController@getByEvento']);
        
        // Route::resource('ouvintes', 'OuvintesController');
        // Route::get('/ouvintes/indexByEvento/{id}', ['uses' => 'OuvintesController@getByEvento']);
        // Route::post('/ouvintes/searchByNome', ['uses' => 'OuvintesController@searchByNome']);
        

        // Route::resource('clientes', 'ClienteController');
        // Route::post('/clientes/getByName', ['uses' => 'ClienteController@findByName']);
        
        // Route::resource('participacao', 'ParticipacaoController');
        // Route::get('/participacao/searchByEvento/{id}', ['uses' => 'ParticipacaoController@getPartipacoesByEvento']);

        // Route::resource('vigencias', 'VigenciasController');
        // Route::get('/vigencias/searchInEvento/{id}', ['uses' => 'VigenciasController@searchResiduosByEvento']);

        // Route::resource('reciclagem', 'ReciclagemsController');
        // Route::get('/reciclagem/searchByParticipacao/{id}', ['uses' => 'ReciclagemsController@getByParticipacaoId']);

        // Route::resource('campanha', 'CampanhasController');
        // Route::get('/campanha/getAtivasByContrato/{contrato_id}', ['uses' => 'CampanhasController@getCampanhasAtivasByContrato']);
        // Route::post('/campanha/participar/{reciclagem_id}', ['uses' => 'CampanhasController@participar']);
        
        // Route::post('/mail/send/{layout}', ['uses' => 'MailController@send']);
        // Route::post('/sms/send/{layout}', ['uses' => 'SmsController@send']);
    });
});
