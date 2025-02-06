<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Emision extends Model
{
    use HasFactory;
    protected $table = 'Emision'; 
    
    public function ordenFabricacion()
    {
        return $this->belongsTo(OrdenFabricacion::class, 'OrdenFabricacion_id');
    }
}
