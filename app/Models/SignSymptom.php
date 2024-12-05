<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignSymptom extends Model
{
    use HasFactory;

    protected $fillable = [
        'week_id',
        'symptoms',
    ];

    public function tips(){
        return $this->hasMany(Tip::class, 'sign_symptoms_id');
    }
}
