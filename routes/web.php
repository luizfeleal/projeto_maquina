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

Route::get('/dashboard', function(){
    return 'teste';
})->name('dashboard');

Route::get('/login', 'App\Http\Controllers\LoginController@login')->name('login-view');
Route::get('/logout', 'App\Http\Controllers\LoginController@logout')->name('logout');
Route::post('/autenticar', 'App\Http\Controllers\LoginController@autenticarUsuario')->name('autenticar');
Route::get('/auth', 'App\Http\Controllers\LoginController@auth')->name('login-auth');