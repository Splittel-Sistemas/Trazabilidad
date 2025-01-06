<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name']; 

    public function roles()
{
    return $this->belongsToMany(Role::class, 'permission_role','permission_id', 'role_id');  // Esta relación está bien
}

     public function users()
    {
        return $this->belongsToMany(User::class);
    } 

}