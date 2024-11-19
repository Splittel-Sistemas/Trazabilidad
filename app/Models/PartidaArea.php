<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidaArea extends Model
{
    use HasFactory;

    protected $table = 'partidas_area';

    public function partidaOf()
    {
        return $this->belongsTo(PartidaOf::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}