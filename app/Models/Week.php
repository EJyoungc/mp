<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    use HasFactory;

    protected $fillable = [
        'week',
        'trimester_id',
    ];

    protected static function booted()
    {
        static::deleting(function (Week $week) {
            foreach ($week->tips as $tip) {
                $tip->delete();
            }

            // Delete MessageHistory records associated with this week
            MessageHistory::where('week_id', $week->id)->delete();
        });
    }

    public function tips(){
        return $this->hasMany(Tip::class);
    }

    public function trimester(){

        return $this->belongsTo(Trimester::class);
    }


}
