<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role_id',
        'staff_id',
        'partner_id',
        'status',
        'organization_id',
        'created_by',
        'updated_by',
    ];

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function school(){
        return $this->belongsTo(School::class);
    }

    public function permissions(){
         return $this->role ? $this->role->permissions : collect();
    }

    public function hasPermission($pageEndpoint, $action){
        return $this->role->permissions()->whereHas('page', function ($query) use ($pageEndpoint) {
            $query->where('endpoint', $pageEndpoint);
        })->where($action, 1)->exists();
    }
}
