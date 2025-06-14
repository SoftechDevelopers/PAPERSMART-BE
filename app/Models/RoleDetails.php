<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleDetails extends Model
{
    use HasFactory;

    protected $table = 'role_details';

    protected $fillable = [
        'role_id',
        'portal_page_id',
        'create',
        'view',
        'edit',
        'remove',
        'export',
        'print',
        'send',
        'created_by',
        'updated_by'
    ];

    public function portalPage()
    {
        return $this->belongsTo(PortalPage::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
