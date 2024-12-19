<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenFabricacion extends Model
{
    use HasFactory;
    protected $table = 'OrdenFabricacion'; // Ajuste: Nombre de la tabla en minúsculas


    // Relación con OrdenVenta
    public function ordenVenta()
    {
        return $this->belongsTo(OrdenVenta::class, 'OrdenVenta_id');
    }

    // Relación con PartidasOF
    public function partidasOF()
    {
        return $this->hasMany(PartidasOF::class, 'OrdenFabricacion_id');
    } 
}
