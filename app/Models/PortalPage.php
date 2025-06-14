<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalPage extends Model
{
    use HasFactory;

    protected $table = 'portal_page';

    protected $fillable = [
        'name',
        'type'
    ];

    public function roleDetails()
    {
        return $this->hasMany(RoleDetails::class, 'page_id');
    }
}
