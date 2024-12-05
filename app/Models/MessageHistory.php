<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'tip_id',
        'week_id',
        'day_range_id',
        'day_id',
        'mother_id',
        'history_id',
        'message_status',
    ];


    public function tip(){
        return $this->belongsTo(Tip::class);
    }
    public function day_ranger(){   
        return $this->belongsTo(DayRange::class);
    }
    public function day(){
        return $this->belongsTo(Day::class);
    }

    public function week(){
        return $this->belongsTo(Week::class);
    }

    public function mother(){
        return $this->belongsTo(User::class,'mother_id');
    }


}
