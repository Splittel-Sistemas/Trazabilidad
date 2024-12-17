<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrosBuffer extends Model
{

    use HasFactory;
    public function FechasBuffer()
    {
        return $this->belongsTo(RegistrosBuffer::class, 'Fechas_Buffer_id');
    }
}
