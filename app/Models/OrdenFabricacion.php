<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenFabricacion extends Model
{
    use HasFactory;

    protected $table = 'orden_fabricacion';
    protected $fillable = ['orden_venta_id', 'numero_fabricacion', 'fecha_fabricacion', 'estado'];

 
    public function ordenVenta()
    {
        return $this->belongsTo(OrdenVenta::class, 'orden_venta_id');
    }

  
    public function partidas()
    {
        return $this->hasMany(PartidasOF::class, 'orden_fabricacion_id');
    }
}
