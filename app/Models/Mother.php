<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mother extends Model
{
    use HasFactory;

    protected $fillable = [

        'name',
        'dob',
        'marital_status',
        'occupation',
        'address',
        'phone',
        'next_of_kin',
        'next_of_kin_phone',
        'Last_normal_period_date',
        'traditional_Authority',

    ];
   
  
}
