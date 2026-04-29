<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'organization_id',
        'role_id',
        'token',
        'expires_at',
        'registered_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'registered_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
