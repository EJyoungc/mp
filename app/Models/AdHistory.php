<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'mother_id',
        'pharmacy_ad_id',
        'organization_id',
        'message',
        'status',
        'api_response',
    ];

    protected $casts = [
        'api_response' => 'array',
    ];

    public function mother()
    {
        return $this->belongsTo(User::class, 'mother_id');
    }

    public function pharmacyAd()
    {
        return $this->belongsTo(PharmacyAd::class, 'pharmacy_ad_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
