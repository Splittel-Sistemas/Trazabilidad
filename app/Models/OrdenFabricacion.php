<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenFabricacion extends Model
{
    use HasFactory;

    protected $table = 'orden_fabricacion';

    public function ordenVenta()
    {
        return $this->belongsTo(OrdenVenta::class);
    }

    public function partidasOf()
    {
        return $this->hasMany(PartidaOf::class);
    }
}