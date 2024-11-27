<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidasArea extends Model
{
    use HasFactory;

    protected $table = 'partidas_area';
    protected $fillable = ['partida_of_id', 'area_id', 'actividad'];

    public function partidaOf()
    {
        return $this->belongsTo(PartidasOF::class, 'partida_of_id');
    }

   
    public function area()
    {
        return $this->belongsTo(Areas::class, 'area_id');
    }
}
