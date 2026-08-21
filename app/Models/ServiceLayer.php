<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceLayer extends Model
{
    use HasFactory;
    protected $table = 'service_layer';
    public $timestamps = false;

    public function traslado(): HasOne
    {
        return $this->hasOne(Traslados::class);
    }

    public function usuarioRecibe(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'usuario_recive_id');
    }
}
