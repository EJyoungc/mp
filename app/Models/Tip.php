<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    use HasFactory;
    protected $fillable = ['tip', 'week_id','day_range_id','day_id'];

    public function week(){
        return $this->belongsTo(Week::class);
    }

    public function day_range(){
        return $this->belongsTo(DayRange::class);
    }

    public function day(){
        return $this->belongsTo(Day::class);
    }








}

