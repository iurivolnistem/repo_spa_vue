<?php

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\User;
use App\Conteudo;

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

Route::post('/cadastro', 'UsuarioController@cadastro');
Route::post('/login', 'UsuarioController@login');
Route::middleware('auth:api')->put('/perfil', 'UsuarioController@perfil');
Route::middleware('auth:api')->post('/conteudo/adicionar', 'ConteudoController@adicionar');
Route::middleware('auth:api')->get('/conteudo/lista', 'ConteudoController@lista');
Route::middleware('auth:api')->put('/conteudo/curtida/{id}', 'ConteudoController@curtir');
Route::middleware('auth:api')->put('/conteudo/comentar/{id}', 'ConteudoController@comentar');
Route::middleware('auth:api')->get('/conteudo/pagina/lista/{id}', 'ConteudoController@pagina');

Route::middleware('auth:api')->post('/usuario/amigo', 'UsuarioController@amigo');
Route::middleware('auth:api')->get('/usuario/listamigos', 'UsuarioController@listAmigos');
Route::middleware('auth:api')->get('/usuario/listAmigosUsuario/{id}', 'UsuarioController@listAmigosUsuario');

Route::get('/testes', function(){

    $user = User::find(1);
    $conteudo = Conteudo::find(12);
    $user->comentarios()->create([
        'conteudo_id' => $conteudo->id,
        'texto'       => 'Texto de teste do Comentário!',
        'data'        => date('y-m-d H:i:s')
    ]);

    /* Adicionar Conteudo
     $user->conteudos()->create([
        'titulo'=> 'Conteúdo 3',
        'texto' => 'Descrião de Conteúdo!',
        'imagem' => 'Url da Imagem',
        'link' => 'Link',
        'data' => '2019-08-27'
    ]);
    return $user->conteudos;
    */

    /* Adicionar Amigo
    $user->amigos()->toggle($user2->id);
    return $user->amigos;
    */

    /* Adicionar Curtidas
    $conteudo = Conteudo::find(1);
    $user->curtidas()->toggle($conteudo->id);
    return $user->curtidas;
    */

});
