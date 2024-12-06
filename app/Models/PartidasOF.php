<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidasOF extends Model
{
    use HasFactory;

    protected $table = 'PartidasOF';
    //protected $fillable = ['orden_fabricacion_id', 'codigo_partida', 'cantidad', 'costo_unitario'];

    /*public function ordenFabricacion()
    {
        return $this->belongsTo(OrdenFabricacion::class, 'orden_fabricacion_id');
    }*/
    public function OrdenFabricacion()
    {
        return $this->belongsTo('App\Models\OrdenFabricacion','id');
    }
    public function partidasArea()
    {
        return $this->hasMany(PartidasArea::class, 'partida_of_id');
    }
}
