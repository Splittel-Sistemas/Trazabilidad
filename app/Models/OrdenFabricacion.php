<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenFabricacion extends Model
{
    use HasFactory;
    protected $table = 'OrdenFabricacion';
    //protected $fillable = ['orden_venta_id', 'numero_fabricacion', 'fecha_fabricacion', 'estado'];
    //Relacion 1 a muchos OrdenVenta
    public function OrdenVenta()
    {
        return $this->belongsTo('App\Models\OrdenVenta','id');
    }
    public function PartidaOFs()
    {
        return $this->hasMany('App\Models\PartidasOF','OrdenFabricacion_id');
    }

    /*public function partidas()
    {
        return $this->hasMany(PartidasOF::class, 'orden_fabricacion_id');
    }*/
}
