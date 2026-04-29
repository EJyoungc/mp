<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'website',
        'address',
        'description',
        'owner_id',
        'is_pharmacy',
        'district_id',
        'area_id',
    ];

    protected $casts = [
        'is_pharmacy' => 'boolean',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }
}
