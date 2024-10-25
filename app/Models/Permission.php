<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name']; // Campos que se pueden llenar masivamente

    public function roles()
    {
        return $this->belongsToMany(Role::class); // Relación con el modelo Role
    }
     public function users()
    {
        return $this->belongsToMany(User::class);
    }

}