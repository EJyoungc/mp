<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    use BelongsToOrganization;
    use HasFactory;

    const STATUS_DRAFT = 'draft';

    const STATUS_PENDING = 'pending_approval';

    const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'tip',
        'week_id',
        'day_range_id',
        'day_id',
        'organization_id',
        'created_by',
        'approved_by',
        'status',
        'is_template',
    ];

    public function week()
    {
        return $this->belongsTo(Week::class);
    }

    public function day_range()
    {
        return $this->belongsTo(DayRange::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include approved tips.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    protected static function booted()
    {
        static::deleting(function (Tip $tip) {
            // Delete MessageHistory records associated with this tip to maintain integrity
            MessageHistory::where('tip_id', $tip->id)->delete();
        });
    }
}
