<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidasOF extends Model
{

    use HasFactory;
    protected $table = 'partidasof'; 
    /*protected $fillable = [
        'orden_fabricacion_id',
        'cantidad_partida',
        'fecha_fabricacion',
            
    ];*/

    public function ordenFabricacion()
    {
        return $this->belongsTo(OrdenFabricacion::class, 'OrdenFabricacion_id');
    }

    public function partidasArea()
    {
        return $this->hasMany(PartidasArea::class, 'PartidasOF_id');
    } 

    public function Partidas()
    {
        return $this->hasMany(Partidas::class, 'PartidasOF_id');
    } 

}
