<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidasOF extends Model
{

    use HasFactory;
    protected $table = 'partidasof'; 
    protected $fillable = [
        'OrdenFabricacion_id',
        'cantidad_partida',
        'fecha_fabricacion',
        'FechaFinalizar',
            
    ];

    public function ordenFabricacion()
    {
        return $this->belongsTo(OrdenFabricacion::class, 'OrdenFabricacion_id');
    }
    
    /*public function partidasArea()
    {
        return $this->hasMany(PartidasArea::class, 'PartidasOF_id');
    }*/ 
    public function Areas()
    {
        return $this->belongsToMany(Areas::class,'PartidasOF_Areas','PartidasOF_id','Areas_id')
        ->withPivot('FechaComienzo', 'FechaTermina', 'Areas_id', 'Linea_id','Cantidad','id','TipoPartida','NumeroEtiqueta');
    }
    public function Partidas()
    {
        return $this->hasMany(Partidas::class, 'PartidasOF_id');
    } 

}
