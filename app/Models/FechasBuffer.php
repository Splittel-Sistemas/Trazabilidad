<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FechasBuffer extends Model
{

    use HasFactory;

    protected $table = 'FechasBuffer'; 
    
    public function RegistroBuffers()
    {
        return $this->hasMany(RegistrosBuffer::class, 'FechasBuffer_id');
    }
}
