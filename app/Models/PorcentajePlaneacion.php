<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PorcentajePlaneacion extends Model
{
    protected $table = 'PorcentajePlaneacion'; // Nombre de la tabla sin cambios

    public function Linea()
    {
        return $this->belongsTo(Linea::class, 'Linea_id', 'id');
    }
}
