<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'description',
        'ad_message',
        'target_week_start',
        'target_week_end',
        'image_path',
        'is_active',
        'total_sent',
        'organization_id',
        'trimester_id',
        'schedule_type',
        'schedule_limit',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'trimester_id' => 'integer',
        'schedule_limit' => 'integer',
    ];

    public function trimester()
    {
        return $this->belongsTo(Trimester::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope for active ads.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
