<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Conteudo;
use App\User;

class ConteudoController extends Controller
{
    public function adicionar(Request $request){
        $data = $request->all();
        $user = $request->user();

        $valid = Validator::make($data, [
            'titulo' => 'required|max:255',
            'texto'  => 'required|max:500',
        ]);

        if($valid->fails()){
            return ['status' => false, 'validacao' => true, 'erros' => $valid->errors()];
        }

        $conteudo = new Conteudo();
        $conteudo->titulo = $data['titulo'];
        $conteudo->texto  = $data['texto'];
        $conteudo->imagem = $data['imagem'] ? $data['imagem'] : '#';
        $conteudo->link   = $data['link']   ? $data['link']   : '#';
        $conteudo->data   = date('Y-m-d H:i:s');

        $user->conteudos()->save($conteudo);
        $conteudos = Conteudo::with('user')->orderby('data','DESC')->paginate(5);
        return ['status' => true, 'conteudos' => $conteudos];
    }

    public function lista(Request $request){

        $conteudos = Conteudo::with('user')->orderby('data','DESC')->paginate(5);
        $user = $request->user();

        foreach ($conteudos as $key => $conteudo) {
            $conteudo->total_curtidas = $conteudo->curtidas()->count();
            $conteudo->comentarios = $conteudo->comentarios()->with('user')->get();
            $curtiu = $user->curtidas()->find($conteudo->id);
            if($curtiu){
                $conteudo->conteudo_curtido = true;
            }else{
                $conteudo->conteudo_curtido = false;
            }
        }

        return ['status' => true, 'conteudos' => $conteudos];
    }

    public function curtir($id, Request $request){
        $conteudo = Conteudo::find($id);
        if($conteudo){
            $user = $request->user();
            $user->curtidas()->toggle($conteudo->id);
            return ['status' => true, 'curtidas' => $conteudo->curtidas()->count(), 'lista' => $this->lista($request)];
        }else{
            return ['status' => false, 'erro' => 'Este conteúdo não está mais disponível!'];
        }
    }

    public function comentar($id, Request $request){
        $conteudo = Conteudo::find($id);
        if($conteudo){
            $user = $request->user();
            $user->comentarios()->create([
                'conteudo_id' => $conteudo->id,
                'texto'       => $request->texto,
                'data'        => date('y-m-d H:i:s')
            ]);
            return ['status' => true, 'lista' => $this->lista($request)];
        }else{
            return ['status' => false, 'erro' => 'Este cometário não está mais disponível!'];
        }
    }

    public function pagina($id, Request $request){
        $paginaUsuario = User::find($id);
        if($paginaUsuario){
            $conteudos = $paginaUsuario->conteudos()->with('user')->orderby('data','DESC')->paginate(5);
            $user = $request->user();

        foreach ($conteudos as $key => $conteudo) {
            $conteudo->total_curtidas = $conteudo->curtidas()->count();
            $conteudo->comentarios = $conteudo->comentarios()->with('user')->get();
            $curtiu = $user->curtidas()->find($conteudo->id);
                if($curtiu){
                    $conteudo->conteudo_curtido = true;
                }else{
                    $conteudo->conteudo_curtido = false;
                }
            }
            return ['status' => true, 'conteudos' => $conteudos, 'dono' => $paginaUsuario];
        }else{
            return ['status' => false,'erro' => 'Usuário não existe.'];
        }
    }
}
