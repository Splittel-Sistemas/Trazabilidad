<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';

    
    public function partidasArea()
    {
        
        return $this->hasMany(PartidasArea::class);
    }

    
    protected $fillable = [
        'nombre',  
    ];
}
