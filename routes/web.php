<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('maquinas')->group(function(){
    Route::get('/', 'App\Http\Controllers\MaquinasController@coletarTodasAsMaquinas')->name('maquinas');
});

Route::prefix('local')->group(function(){
    Route::get('/', 'App\Http\Controllers\LocaisController@coletarLocais')->name('local');
    Route::get('/criar', 'App\Http\Controllers\LocaisController@criarLocais')->name('local-criar');
    Route::get('/registrar', 'App\Http\Controllers\LocaisController@registrarLocais')->name('local-registrar');
});

Route::get('/login', 'App\Http\Controllers\LoginController@login')->name('login-view');
Route::get('/logout', 'App\Http\Controllers\LoginController@logout')->name('logout');
Route::get('/redefinirView', 'App\Http\Controllers\LoginController@redefinir')->name('login-redefinir-view');
Route::post('/redefinirConfirmacao', 'App\Http\Controllers\LoginController@enviarEmailRedefinir')->name('login-redefinir-confirmar');
Route::get('/login/senha/alterar', 'App\Http\Controllers\LoginController@novaSenhaView')->name('login-redefinir-senha-view');
Route::post('/criarSenhaRegistrar', 'App\Http\Controllers\LoginController@registrarSenha')->name('login-redefinir-registrar-senha');
Route::post('/autenticar', 'App\Http\Controllers\LoginController@autenticarUsuario')->name('autenticar');
Route::get('/auth', 'App\Http\Controllers\LoginController@auth')->name('login-auth');
