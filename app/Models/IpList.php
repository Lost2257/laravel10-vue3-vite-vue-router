<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpList extends Model
{
    protected $fillable = ['name', 'email', 'password', 'role', 'ip_address'];
    use HasFactory;
}
