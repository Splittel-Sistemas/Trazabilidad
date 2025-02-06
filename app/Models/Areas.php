<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{

    use HasFactory;
    public function PartidasOF(){
        return $this->belongsToMany(PartidasOF::class,'Partidas_Areas','PartidasOF_id','Areas_id','Cantidad','TipoPartida');
    }
}
