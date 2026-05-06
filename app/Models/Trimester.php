<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trimester extends Model
{
    use HasFactory;



    protected $fillable = [
        'trimester'
    ];

    protected static function booted()
    {
        static::deleting(function (Trimester $trimester) {
            throw new \Exception('Deletion of trimesters is not allowed.');
        });
    }

    public function weeks()
    {
        return $this->hasMany(Week::class);
    }

    
}
