<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ['name', 'system_name', 'db_name', 'active'];
}
