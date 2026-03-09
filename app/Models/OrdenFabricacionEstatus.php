<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenFabricacionEstatus extends Model
{
    use HasFactory;
    protected $table = 'ordenfabricacion_estatus';
    
    public function ordenVenta(){
        return $this->belongsTo(OrdenFabricacion::class, 'OrdenFabricacion_id');
    }
}
