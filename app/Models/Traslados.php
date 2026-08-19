<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Traslados extends Model
{
    use HasFactory;
    protected $table = 'traslados'; 
    protected $fillable = ['estado', 'usuario_traslado_id', 'usuario_recive_id'];

    public function trasladoDetalles(): HasMany
    {
        return $this->hasMany(TrasladoDetalle::class, 'traslado_id', 'id');
    }

    public function serviceLayer(): HasMany
    {
        return $this->hasMany(ServiceLayer::class, 'traslado_id', 'id');
    }

    public function usuarioTraslado(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'usuario_traslado_id');
    }

    public function usuarioRecibe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_recive_id', 'id');
    }
}
