<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\User;
use Auth;
use Storage;

class UsuarioController extends Controller
{
    public function login(Request $request){
        $data = $request->all();

        $valid = Validator::make($data, [
            'email' => 'required|string|max:255',
            'password' => 'required'
        ]);

        if($valid->fails()){
            return ['status' => false, 'validacao' => true, 'erros' => $valid->errors()];
        }
        if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
            $user = auth()->user();
            $user->token = $user->createToken($user->email)->accessToken;
            //$user->imagem = asset($user->imagem);
            return ['status' => true, 'usuario' => $user];
        }else{
            return ['status' => false];
        }
    }

    public function cadastro(Request $request){
        $data = $request->all();
        $valid = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);
        if($valid->fails()){
            return ['status' => false, 'validacao' => true, 'erros' => $valid->errors()];
        }
        $imagem = '/perfils/default.jpg';

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'imagem' =>  $imagem
        ]);
        $user->token = $user->createToken($user->email)->accessToken;
        //$user->imagem = asset($user->imagem);
        return ['status' => true, 'usuario' => $user];
    }

    public function perfil(Request $request){
      $user = $request->user();
      $data = $request->all();

      if(isset($data['password'])){
        $valiacao = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user->id)],
            'password' => 'required|string|min:6|confirmed',
        ]);
        if($valiacao->fails()){
          return ['status'=>false,"validacao"=>true,"erros"=>$valiacao->errors()];
        }
        $user->password = bcrypt($data['password']);

      }else{
        $valiacao = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user->id)],
        ]);

        if($valiacao->fails()){
          return ['status'=>false,"validacao"=>true,"erros"=>$valiacao->errors()];
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
      }

      if(isset($data['imagem'])){

        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
              $explode = explode(',', $value);
              $allow = ['png', 'jpg', 'svg','jpeg'];
              $format = str_replace(
                  [
                      'data:image/',
                      ';',
                      'base64',
                  ],
                  [
                      '', '', '',
                  ],
                  $explode[0]
              );
              // check file format
              if (!in_array($format, $allow)) {
                  return false;
              }
              // check base64 format
              if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                  return false;
              }
              return true;
          });

          $valiacao = Validator::make($data, [
              'imagem' => 'base64image',

          ],['base64image'=>'Imagem inválida']);

          if($valiacao->fails()){
            return ['status'=>false,"validacao"=>true,"erros"=>$valiacao->errors()];
          }
        $time = time();
        $diretorioPai = 'perfils';
        $diretorioImagem = $diretorioPai.DIRECTORY_SEPARATOR.'perfil_id'.$user->id;
        $ext = substr($data['imagem'], 11, strpos($data['imagem'], ';') - 11);
        $urlImagem = $diretorioImagem.DIRECTORY_SEPARATOR.$time.'.'.$ext;

        $file = str_replace('data:image/'.$ext.';base64,','',$data['imagem']);
        $file = base64_decode($file);

        if(!file_exists($diretorioPai)){
          mkdir($diretorioPai,0700);
        }
        if($user->imagem){
            $imgUser = str_replace(asset('/'),'',$user->imagem);
          if(file_exists($imgUser)){
            unlink($imgUser);
          }
        }
        if(!file_exists($diretorioImagem)){
          mkdir($diretorioImagem,0700);
        }
        Storage::put($urlImagem,$file);
        $user->imagem = $urlImagem;
      }
      $user->save();

      //$user->imagem = asset($user->imagem);
      $user->token = $user->createToken($user->email)->accessToken;
      return ['status'=>true,"usuario"=>$user];
    }

    public function amigo(Request $request){
        $user = $request->user();
        $amigo = User::find($request->id);
        if($amigo && ($user->id != $request->id)){
            $user->amigos()->toggle($amigo->id);
            return ['status' => true, 'amigos' => $user->amigos()];
        }else{
            return ['status' => false, 'amigos' => 'Este usuário não existe.' ];
        }
    }

    public function listAmigos(Request $request){
        $user = $request->user();
        if($user){
            return ['status' => true, 'amigos' => $user->amigos];
        }else{
            return ['status' => false, 'erro' => 'Este usuário não existe!'];
        }

    }

    public function listAmigosUsuario($id, Request $request){
        $user = User::find($id);
        $userLogado = $request->user();
        if($user){
            return ['status' => true, 'amigos' => $user->amigos, "amigosLogado" => $userLogado->amigos];
        }else{
            return ['status' => false, 'erro' => 'Este usuário não existe!'];
        }
    }
}
