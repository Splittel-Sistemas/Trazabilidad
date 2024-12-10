<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidasOF extends Model
{
    use HasFactory;

    protected $table = 'partidas_of'; // Ajuste: Nombre en minúsculas
    protected $fillable = ['orden_fabricacion_id', 'cantidad_partida', 'fecha_fabricacion'];

    // Relación con OrdenFabricacion
    public function ordenFabricacion()
    {
        return $this->belongsTo(OrdenFabricacion::class, 'orden_fabricacion_id');
    }

    // Relación con PartidasArea
    public function partidasArea()
    {
        return $this->hasMany(PartidasArea::class, 'partida_of_id');
    }
}

