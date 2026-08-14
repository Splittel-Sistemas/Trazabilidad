<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Traslados extends Model
{
    use HasFactory;
    protected $table = 'traslados'; 

    public function trasladoDetalles(): HasMany
    {
        return $this->hasMany(TrasladoDetalle::class, 'id_traslado', 'id');
    }

    public function ServiceLayer(): BelongsTo
    {
        return $this->belongsTo(ServiceLayer::class, 'id', 'id_traslado');
    }

    public function usuarioTraslado(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'id_usuario_traslado');
    }

    public function usuarioRecibe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_recive', 'id');
    }
}
