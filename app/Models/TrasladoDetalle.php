<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrasladoDetalle extends Model
{
    use HasFactory;
    protected $table = 'traslado_detalles';

    public function traslados(): HasOne
    {
        return $this->hasOne(Traslados::class);
    }
}
