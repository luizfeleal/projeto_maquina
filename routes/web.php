<?php

use Illuminate\Support\Facades\Route;
use App\Services\AcessosTelaService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('maquinas')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\MaquinasController@coletarTodasAsMaquinas')->name('maquinas');
    Route::get('/criar', 'App\Http\Controllers\MaquinasController@criarMaquinas')->name('maquinas-criar');
    Route::get('/registrar', 'App\Http\Controllers\MaquinasController@registrarMaquinas')->name('maquinas-registrar');
    Route::get('/gerarIdPlaca', 'App\Http\Controllers\MaquinasController@gerarIdPlaca')->name('maquinas-gerar-id-placa');
    Route::get('/transacoes', 'App\Http\Controllers\MaquinasController@transacaoMaquinas')->name('maquinas-transacoes');
    Route::get('/acumulado', 'App\Http\Controllers\MaquinasController@acumuladoMaquinas')->name('maquinas-acumulado');
});

Route::prefix('local')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\LocaisController@coletarLocais')->name('local');
    Route::get('/incluirUsuario', 'App\Http\Controllers\LocaisController@incluirUsuarioLocal')->name('local-incluir-usuario');
    Route::get('/criar', 'App\Http\Controllers\LocaisController@criarLocais')->name('local-criar');
    Route::get('/registrar', 'App\Http\Controllers\LocaisController@registrarLocais')->name('local-registrar');
});
Route::prefix('usuarios')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\ClientesController@coletarCliente')->name('usuarios');
    Route::get('/criar', 'App\Http\Controllers\ClientesController@criarCliente')->name('usuario-criar');
    Route::get('/registrar', 'App\Http\Controllers\ClientesController@registrarCliente')->name('usuario-registrar');
});
Route::prefix('qr')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\QrCodeController@coletarQr')->name('qr');
    Route::get('/criar', 'App\Http\Controllers\QrCodeController@criarQr')->name('qr-criar');
    Route::get('/registrar', 'App\Http\Controllers\QrCodeController@registrarQr')->name('qr-registrar');
    Route::get('/download', 'App\Http\Controllers\QrCodeController@downloadQr')->name('qr-download');
});

Route::prefix('relatorios')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\RelatoriosController@view')->name('relatorio-view');
    Route::post('/exibir', 'App\Http\Controllers\RelatoriosController@exibirRelatorio')->name('relatorio-criar');
    Route::get('/download', 'App\Http\Controllers\RelatoriosController@downloadXlsxRelatorio')->name('relatorio-xlsx-download');
});

Route::get('/login', 'App\Http\Controllers\LoginController@login')->name('login-view');
Route::get('/logout', 'App\Http\Controllers\LoginController@logout')->name('logout');
Route::get('/redefinirView', 'App\Http\Controllers\LoginController@redefinir')->name('login-redefinir-view');
Route::post('/redefinirConfirmacao', 'App\Http\Controllers\LoginController@enviarEmailRedefinir')->name('login-redefinir-confirmar');
Route::get('/login/senha/alterar', 'App\Http\Controllers\LoginController@novaSenhaView')->name('login-redefinir-senha-view');
Route::post('/criarSenhaRegistrar', 'App\Http\Controllers\LoginController@registrarSenha')->name('login-redefinir-registrar-senha');
Route::post('/autenticar', 'App\Http\Controllers\LoginController@autenticar')->name('autenticar');
Route::get('/auth', 'App\Http\Controllers\LoginController@auth')->name('login-auth');
