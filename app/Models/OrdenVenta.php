<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenVenta extends Model
{
    use HasFactory;

    protected $table = 'orden_venta'; 
    protected $fillable = ['numero_orden', 'fecha_venta', 'cliente', 'total_venta']; 

    
    public function ordenesFabricacion()
    {
        return $this->hasMany(OrdenFabricacion::class, 'orden_venta_id');
    }
}
