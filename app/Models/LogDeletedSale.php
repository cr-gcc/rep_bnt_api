<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogDeletedSale extends Model
{
  protected $table = 'log_deleted_sales';

  protected $fillable = [
    'user_id',
    'data_base',
    'certificate',
  ];
}
