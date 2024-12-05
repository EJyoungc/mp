<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    use HasFactory;


    public function tips(){
        return $this->hasMany(Tip::class);
    }

    public function trimester(){

        return $this->belongsTo(Trimester::class);
    }


}
