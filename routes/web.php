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
    return redirect()->route('login-view');
});

Route::prefix('home')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\HomeController@coletar')->name('home');
});
Route::prefix('cliente-home')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\Clientes\HomeController@coletar')->name('cliente-home');
});

Route::get('/coletarCredencialDescriptografada', 'App\Http\Controllers\CredenciaisController@coletarCredenciais');

Route::prefix('maquinas')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\MaquinasController@coletarTodasAsMaquinas')->name('maquinas');
    Route::get('/criar', 'App\Http\Controllers\MaquinasController@criarMaquinas')->name('maquinas-criar');
    Route::get('/editar', 'App\Http\Controllers\MaquinasController@editarMaquinas')->name('maquinas-editar');
    Route::get('/visualizar', 'App\Http\Controllers\MaquinasController@coletarMaquinaPorId')->name('maquinas-visualizar');
    Route::get('/registrar', 'App\Http\Controllers\MaquinasController@registrarMaquinas')->name('maquinas-registrar');
    Route::get('/gerarIdPlaca', 'App\Http\Controllers\MaquinasController@gerarIdPlaca')->name('maquinas-gerar-id-placa');
    Route::get('/transacoes', 'App\Http\Controllers\MaquinasController@transacaoMaquinas')->name('maquinas-transacoes');
    Route::get('/acumulado', 'App\Http\Controllers\MaquinasController@acumuladoMaquinas')->name('maquinas-acumulado');
    Route::get('/maquinasCartao', 'App\Http\Controllers\MaquinasController@viewMaquinasCartao')->name('maquinas-cartao');
    Route::get('/maquinasCartao/criar', 'App\Http\Controllers\MaquinasController@viewMaquinasCartaoCriar')->name('maquinas-cartao-criar');
    Route::post('/maquinasCartao/registrar', 'App\Http\Controllers\MaquinasController@registrarMaquinasCartao')->name('maquinas-cartao-registrar');
    Route::post('/maquinasCartao/inativar', 'App\Http\Controllers\MaquinasController@inativarMaquinasCartao')->name('maquinas-cartao-inativar');
    Route::post('/excluir', 'App\Http\Controllers\MaquinasController@excluirMaquinas')->name('maquinas-excluir');
    Route::post('/atualizar', 'App\Http\Controllers\MaquinasController@atualizarMaquina')->name('maquinas-atualizar');
    Route::post('/liberarJogadaRegistrar', 'App\Http\Controllers\MaquinasController@liberarJogada')->name('maquinas-liberar-jogada');
    Route::get('/liberarJogada', 'App\Http\Controllers\MaquinasController@viewLiberarJogada')->name('view-liberar-jogadas');
});

Route::prefix('clientes-maquinas')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\Clientes\MaquinasController@coletarTodasAsMaquinas')->name('clientes-maquinas');
    Route::get('/transacoes', 'App\Http\Controllers\Clientes\MaquinasController@transacaoMaquinas')->name('clientes-maquinas-transacoes');
    Route::get('/acumulado', 'App\Http\Controllers\Clientes\MaquinasController@acumuladoMaquinas')->name('clientes-maquinas-acumulado');
    Route::get('/viewLiberarJogada', 'App\Http\Controllers\Clientes\MaquinasController@viewLiberarJogada')->name('view-clientes-maquinas-liberar-jogadas');
    Route::post('/liberarJogada', 'App\Http\Controllers\Clientes\MaquinasController@liberarJogada')->name('clientes-maquinas-liberar-jogadas');
    Route::get('/maquinasCartao', 'App\Http\Controllers\Clientes\MaquinasController@viewMaquinasCartao')->name('cliente-maquinas-cartao');
    Route::get('/maquinasCartao/criar', 'App\Http\Controllers\Clientes\MaquinasController@viewMaquinasCartaoCriar')->name('cliente-maquinas-cartao-criar');
    Route::post('/maquinasCartao/registrar', 'App\Http\Controllers\Clientes\MaquinasController@registrarMaquinasCartao')->name('cliente-maquinas-cartao-registrar');
    Route::post('/maquinasCartao/inativar', 'App\Http\Controllers\Clientes\MaquinasController@inativarMaquinasCartao')->name('cliente-maquinas-cartao-inativar');
});

Route::prefix('clientes-relatorio')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\Clientes\RelatoriosController@view')->name('cliente-relatorio-view');
    Route::post('/exibir', 'App\Http\Controllers\Clientes\RelatoriosController@exibirRelatorio')->name('cliente-relatorio-criar');
    Route::post('/download', 'App\Http\Controllers\Clientes\RelatoriosController@downloadXlsxRelatorio')->name('cliente-relatorio-xlsx-download');
});
Route::prefix('local')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\LocaisController@coletarLocais')->name('local');
    Route::get('/incluirUsuario', 'App\Http\Controllers\LocaisController@incluirUsuarioLocal')->name('local-incluir-usuario');
    Route::get('/registrarUsuarioLocal', 'App\Http\Controllers\LocaisController@registrarUsuarioLocal')->name('local-registrar-usuario');
    Route::get('/criar', 'App\Http\Controllers\LocaisController@criarLocais')->name('local-criar');
    Route::get('/detalhar/{id}', 'App\Http\Controllers\LocaisController@coletarLocaisPorId')->name('local-detalhar');
    Route::get('/registrar', 'App\Http\Controllers\LocaisController@registrarLocais')->name('local-registrar');
    Route::post('/excluir', 'App\Http\Controllers\LocaisController@excluirLocais')->name('local-excluir');
});
Route::prefix('usuarios')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\ClientesController@coletarCliente')->name('usuarios');
    Route::get('/criar', 'App\Http\Controllers\ClientesController@criarCliente')->name('usuario-criar');
    Route::get('/detalhar/{id}', 'App\Http\Controllers\ClientesController@coletarClientePorId')->name('usuario-detalhar');
    Route::get('/editar/{id}', 'App\Http\Controllers\ClientesController@editarCliente')->name('usuario-editar');
    Route::post('/atualizar', 'App\Http\Controllers\ClientesController@atualizarCliente')->name('usuario-atualizar');
    Route::post('/registrar', 'App\Http\Controllers\ClientesController@registrarCliente')->name('usuario-registrar');
    Route::post('/excluir', 'App\Http\Controllers\ClientesController@excluirCliente')->name('usuario-excluir');
});
Route::prefix('qr')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\QrCodeController@coletarQr')->name('qr');
    Route::get('/criar', 'App\Http\Controllers\QrCodeController@criarQr')->name('qr-criar');
    Route::post('/registrar', 'App\Http\Controllers\QrCodeController@registrarQr')->name('qr-registrar');
    Route::get('/download', 'App\Http\Controllers\QrCodeController@downloadQr')->name('qr-download');
    Route::post('/excluir', 'App\Http\Controllers\QrCodeController@excluirQr')->name('qr-excluir');
});

Route::prefix('clientes-qr')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\Clientes\QrCodeController@coletarQr')->name('cliente-qr');
    Route::get('/criar', 'App\Http\Controllers\Clientes\QrCodeController@criarQr')->name('cliente-qr-criar');
    Route::post('/registrar', 'App\Http\Controllers\Clientes\QrCodeController@registrarQr')->name('cliente-qr-registrar');
    Route::get('/download', 'App\Http\Controllers\Clientes\QrCodeController@downloadQr')->name('cliente-qr-download');
    Route::post('/excluir', 'App\Http\Controllers\Clientes\QrCodeController@excluirQr')->name('cliente-qr-excluir');
});

Route::prefix('relatorios')->middleware('permission')->group(function(){
    Route::get('/', 'App\Http\Controllers\RelatoriosController@view')->name('relatorio-view');
    Route::post('/exibir', 'App\Http\Controllers\RelatoriosController@exibirRelatorio')->name('relatorio-criar');
    Route::post('/download', 'App\Http\Controllers\RelatoriosController@downloadXlsxRelatorio')->name('relatorio-xlsx-download');
});

Route::prefix('clientes-credenciais')->middleware('permission')->group(function(){
    Route::get('/criar/efi', 'App\Http\Controllers\Clientes\CredenciaisController@criarCredencialEfi')->name('cliente-credencial-criar-efi');
    Route::get('/criar/pagbank', 'App\Http\Controllers\Clientes\CredenciaisController@criarCredencialPagbank')->name('cliente-credencial-criar-pagbank');
    Route::post('/registrar', 'App\Http\Controllers\Clientes\CredenciaisController@registrarCredencial')->name('cliente-credencial-registrar');
});
Route::prefix('credenciais')->middleware('permission')->group(function(){
    Route::get('/criar/efi', 'App\Http\Controllers\Clientes\CredenciaisController@criarCredencialEfi')->name('credencial-criar-efi');
    Route::get('/criar/pagbank', 'App\Http\Controllers\Clientes\CredenciaisController@criarCredencialPagbank')->name('credencial-criar-pagbank');
    Route::post('/registrar', 'App\Http\Controllers\Clientes\CredenciaisController@registrarCredencial')->name('credencial-registrar');
});

Route::get('/login', 'App\Http\Controllers\LoginController@login')->name('login-view');
Route::get('/logout', 'App\Http\Controllers\LoginController@logout')->name('logout');
Route::get('/redefinirView', 'App\Http\Controllers\LoginController@redefinir')->name('login-redefinir-view');
Route::post('/redefinirConfirmacao', 'App\Http\Controllers\LoginController@enviarEmailRedefinir')->name('login-redefinir-confirmar');
Route::get('/login/senha/alterar', 'App\Http\Controllers\LoginController@novaSenhaView')->name('login-redefinir-senha-view');
Route::post('/criarSenhaRegistrar', 'App\Http\Controllers\LoginController@registrarSenha')->name('login-redefinir-registrar-senha');
Route::post('/autenticar', 'App\Http\Controllers\LoginController@autenticar')->name('autenticar');
Route::get('/auth', 'App\Http\Controllers\LoginController@auth')->name('login-auth');
