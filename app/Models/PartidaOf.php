<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PartidaOf extends Model
{
    use HasFactory;

    protected $table = 'partidas_of';

    public function ordenFabricacion()
    {
        return $this->belongsTo(OrdenFabricacion::class);
    }

    public function partidasArea()
    {
        return $this->hasMany(PartidaArea::class);
    }
}