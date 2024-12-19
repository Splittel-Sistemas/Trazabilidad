<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrosBuffer extends Model
{
    protected $table = 'RegistrosBuffer'; // Ajuste: Nombre de la tabla en minúsculas
    use HasFactory;
    public function FechasBuffer()
    {
        return $this->belongsTo(FechasBuffer::class, 'FechasBuffer_id');
    } 
}
