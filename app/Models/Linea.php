<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{
    use HasFactory;

    protected $table = 'linea'; // Nombre de la tabla sin cambios

    protected $fillable = ['Nombre', 'NumeroLinea', 'Descripcion'];

    public function porcentajePlaneacion()
    {
        return $this->hasMany(PorcentajePlaneacion::class, 'Linea_id', 'NumeroLinea');
    }
    public function ordenesFabricacion()
    {
        return $this->hasMany(OrdenFabricacion::class, 'Linea_id', 'id');
    }

}
