<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenVenta extends Model
{
    use HasFactory;
    protected $table = 'OrdenVenta'; 
    //protected $fillable = [ 'orden_fab','articulo','descripcion','cantidad_of','fecha_entrega',];

    //Relacion 1 a muchos OrdenFabricacion
    public function OrdenFabricacions()
    {
        return $this->hasMany('App\Models\OrdenFabricacion','OrdenVenta_id');
    }
}
