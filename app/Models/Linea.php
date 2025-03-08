<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{
    use HasFactory;

    protected $table = 'linea'; // Asegúrate de que coincide con tu base de datos

    protected $fillable = ['Nombre', 'NumeroLinea', 'Descripcion'];
}