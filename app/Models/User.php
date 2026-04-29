<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        'date_of_birth',
        'role_id',
        'age',
        'religion',
        'marital_status',
        'level_of_education',
        'occupation',
        'next_of_kin',
        'next_of_kin_mobile',
        'address',
        'phone',
        'traditional_authority',
        // 'last_normal_menstrual_period_date',
        'height',
        'leg_or_spine',
        'deformity',
        'deliveries',
        'abortions',
        'still_births',
        'c_section',
        'vacum',
        'multiple',
        'tuberculosis',
        'asthma',
        'menstrual_cycle',
        'organization_id',
        'organization_verify',
        'current_team_id',
        'district_id',
        'area_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [

        'profile_photo_url',
        // 'date_of_birth',
        // 'role_id',
        // 'age',
        // 'religion',
        // 'marital_status',
        // 'level_of_education',
        // 'occupation',
        // 'next_of_kin',
        // 'next_of_kin_mobile',
        // 'address',
        // 'phone',
        // 'traditional_authority',
        // 'last_normal_menstrual_period_date',

    ];

    /**
     * Set the user's date of birth and automatically calculate the age.
     *
     * @param  string  $value  The date of birth (YYYY-MM-DD)
     * @return void
     */
    public function setDateOfBirthAttribute($value)
    {
        $this->attributes['date_of_birth'] = $value;

        if ($value) {
            $this->attributes['age'] = Carbon::parse($value)->age;
        }
    }

    // public function setAttribute($key, $value)
    // {
    //     if (is_string($value)) {
    //         $value = strtolower($value);
    //     }

    //     parent::setAttribute($key, $value);
    // }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'leg_or_spine' => 'string',
            'deformity' => 'string',
            'still_births' => 'string',
            'c_section' => 'string',
            'vacum' => 'string',
            'multiple' => 'string',
            'aph' => 'string',
            'pph' => 'string',
            'pre_eclampsia' => 'string',
            'tuberculosis' => 'string',
            'asthma' => 'string',
            'hypertension' => 'string',
            'diabetes' => 'string',
            'epilepsy' => 'string',
            'renal_disease' => 'string',
            'fistula_repair' => 'string',
            'menstrual_cycle' => 'string',
        ];
    }

    /**
     * Standardize enum fields to lowercase before saving.
     */
    public function setAttribute($key, $value)
    {
        $enums = [
            'leg_or_spine', 'deformity', 'still_births', 'c_section', 'vacum',
            'multiple', 'aph', 'pph', 'pre_eclampsia', 'tuberculosis',
            'asthma', 'hypertension', 'diabetes', 'epilepsy', 'renal_disease',
            'fistula_repair', 'menstrual_cycle',
        ];

        if (in_array($key, $enums) && is_string($value)) {
            $value = strtolower($value);
        }

        return parent::setAttribute($key, $value);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Role Helpers
     */
    public function isSystemAdmin(): bool
    {
        return $this->role && $this->role->name === 'system-admin';
    }

    public function isOrgAdmin(): bool
    {
        return $this->role && ($this->role->name === 'admin' || $this->role->name === 'system-admin');
    }

    public function isPharmacyAdmin(): bool
    {
        return $this->role && $this->role->name === 'admin' && $this->organization && $this->organization->is_pharmacy;
    }

    public function isDoctor(): bool
    {
        return $this->role && ($this->role->name === 'doctor' || $this->isOrgAdmin());
    }

    public function isPractitioner(): bool
    {
        return $this->role && ($this->role->name === 'practitioner' || $this->isDoctor());
    }

    public function isMother(): bool
    {
        return $this->role && $this->role->name === 'mother';
    }
}
