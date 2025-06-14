<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organization';

    protected $fillable = [
        'name',
        'address1',
        'address2',
        'district',
        'state',
        'country',
        'pincode',
        'contact',
        'email',
        'status',
        'logo',
        'created_by',
        'updated_by',
    ];
}
