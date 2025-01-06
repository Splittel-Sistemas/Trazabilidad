<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{

    use HasFactory;
    public function Partidas(){
        return $this->belongsToMany(Partidas::class,'Partidas_Areas','Partidas_id','Areas_id');
    }
}
