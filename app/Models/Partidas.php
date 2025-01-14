<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partidas extends Model
{
    use HasFactory;
    
    public function PartidasOF()
    {
        return $this->belongsTo(PartidasOF::class, 'PartidasOF_id');
    }
    public function Areas()
    {
        return $this->belongsToMany(Areas::class,'Partidas_Areas','Partidas_id','Areas_id')
        ->withPivot('FechaComienzo', 'FechaTermina', 'Areas_id', 'Linea_id');
    }

}
