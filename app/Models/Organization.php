<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    protected static function booted(): void
    {
        static::deleting(function (Organization $organization) {
            // Delete AdHistories for this organization
            AdHistory::where('organization_id', $organization->id)->delete();

            // Delete PharmacyAds and their AdHistories
            $ads = PharmacyAd::where('organization_id', $organization->id)->get();
            foreach ($ads as $ad) {
                AdHistory::where('pharmacy_ad_id', $ad->id)->delete();
                $ad->delete();
            }

            // Delete Invitations
            Invitation::where('organization_id', $organization->id)->delete();
            if (Schema::hasTable('team_invitations')) {
                DB::table('team_invitations')->where('organization_id', $organization->id)->delete();
            }

            // Delete Tips and their message histories
            $tips = Tip::where('organization_id', $organization->id)->get();
            foreach ($tips as $tip) {
                MessageHistory::where('tip_id', $tip->id)->delete();
                $tip->delete();
            }

            // Delete MessageHistory directly belonging to org
            MessageHistory::where('organization_id', $organization->id)->delete();

            // Delete Histories directly belonging to org
            History::where('organization_id', $organization->id)->delete();

            // Delete all Users belonging to this organization (both staff and mothers)
            $users = User::where('organization_id', $organization->id)->get();
            foreach ($users as $user) {
                History::where('mother_id', $user->id)->delete();
                MessageHistory::where('mother_id', $user->id)->delete();
                AdHistory::where('mother_id', $user->id)->delete();
                $user->delete();
            }
        });
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(User::class)->whereHas('role', function ($q) {
            $q->where('name', '!=', 'mother');
        });
    }

    public function mothers(): HasMany
    {
        return $this->hasMany(User::class)->whereHas('role', function ($q) {
            $q->where('name', 'mother');
        });
    }

    public function tips(): HasMany
    {
        return $this->hasMany(Tip::class);
    }

    public function pharmacyAds(): HasMany
    {
        return $this->hasMany(PharmacyAd::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}
