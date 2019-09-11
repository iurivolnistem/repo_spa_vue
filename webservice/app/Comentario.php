<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     protected $fillable = [
        'conteudo_id', 'texto', 'data'
     ];

     public function user(){
         return $this->belongsTo('App\User');
     }

     public function conteudo(){
         return $this->belongsTo('App\Conteudo');
     }

     public function getDataAttribute($value){
         $data = date('d/m/Y - H:i',strtotime($value));
         $data = str_replace(':','h',str_replace('-','às',$data));
         return $data;

     }
}
