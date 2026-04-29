<?php

namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToOrganization(): void
    {
        static::creating(function ($model) {
            if (empty($model->organization_id) && Auth::check() && Auth::user()->organization_id) {
                $model->organization_id = Auth::user()->organization_id;
            }
        });

        static::addGlobalScope('organization', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();

                // system-admin can see everything. Others are scoped to their organization.
                if ($user->role && $user->role->name !== 'system-admin') {
                    if ($user->organization_id) {
                        $builder->where(function ($query) use ($user) {
                            $query->where('organization_id', $user->organization_id);

                            // For Tips, they might also see global tips (where organization_id is null)
                            // if we decide to have a shared pool.
                            // $query->orWhereNull('organization_id');
                        });
                    }
                }
            }
        });
    }

    /**
     * Get the organization that owns the model.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
