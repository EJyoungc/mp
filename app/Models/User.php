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
        'menstrual_cycle'


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
        ];
    }


    public function organization(){
        return $this->belongsTo(Organization::class);
    }


    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
