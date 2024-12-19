<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenVenta extends Model
{
    use HasFactory;

    protected $table = 'OrdenVenta'; 
    
    public function ordenesFabricacions()
    {
        return $this->hasMany(OrdenFabricacion::class, 'OrdenVenta_id');
    } 
}
