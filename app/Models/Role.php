<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';

    protected $fillable = [
        'role_name',
    ];

    public function permissions()
    {
        return $this->hasMany(RoleDetails::class, 'role_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
