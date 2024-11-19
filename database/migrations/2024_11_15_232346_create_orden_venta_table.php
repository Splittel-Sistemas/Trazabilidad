<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenVenta extends Model
{
    use HasFactory;

    protected $table = 'orden_venta';

    public function ordenesFabricacion()
    {
        return $this->hasMany(OrdenFabricacion::class);
    }
}
