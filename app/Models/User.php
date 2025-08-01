<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;
    use Impersonate;


    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'phone_number',
        'profile',
        'lang',
        'subscription',
        'subscription_expire_date',
        'parent_id',
        'is_active',
        'twofa_secret',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function canImpersonate()
    {
        // Example: Only admins can impersonate others
        return $this->type == 'super admin';
    }

    public function totalUser()
    {
        return User::whereNotIn('type', ['tenant', 'maintainer'])->where('parent_id', $this->id)->count();
    }
    public function totalClient()
    {
        return User::where('type','client')->where('parent_id', $this->id)->count();
    }


    public function totalContact()
    {
        return Contact::where('parent_id', '=', parentId())->count();
    }

    public function roleWiseUserCount($role)
    {
        return User::where('type', $role)->where('parent_id', parentId())->count();
    }

    public static function getDevice($user)
    {
        $mobileType = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
        $tabletType = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';
        if (preg_match_all($mobileType, $user)) {
            return 'mobile';
        } else {
            if (preg_match_all($tabletType, $user)) {
                return 'tablet';
            } else {
                return 'desktop';
            }
        }
    }

    public function subscriptions()
    {
        return $this->hasOne('App\Models\Subscription', 'id', 'subscription');
    }


    public function clients()
    {
        return $this->hasOne('App\Models\ClientDetail','user_id','id');
    }

    public static $systemModules = [
        'user',
        'client',
        'service & part',
        'asset',
        'wo request',
        'estimation',
        'work order',
        'service appointment',
        'invoice',
        'contact',
        'notification',
        'note',
        'logged history',
        'settings',
    ];

    public function SubscriptionLeftDay()
    {
        $Subscription = Subscription::find($this->subscription);
        if ($Subscription->interval == 'Unlimited') {
            $return = '<span class="text-success">'.__('Unlimited Days Left').'</span>';
        } else {
            $date1 = date_create(date('Y-m-d'));
            $date2 = date_create($this->subscription_expire_date);
            $diff = date_diff($date1, $date2);
            $days = $diff->format("%R%a");
            if($days > 0) {
                $return = '<span class="text-success">'.$days.__(' Days Left').'</span>';
            } else {
                $return = '<span class="text-danger">'.$days.__(' Days Left').'</span>';
            }
        }


        return $return;
    }

    /**
     * Get the business profile associated with the user
     */
    public function businessProfile()
    {
        return $this->hasOne(BusinessProfile::class);
    }

    /**
     * Get the KYC document associated with the user
     */
    public function kycDocument()
    {
        return $this->hasOne(KycDocument::class);
    }

    /**
     * Check if user has completed business profile
     */
    public function hasCompletedBusinessProfile()
    {
        return $this->businessProfile && $this->businessProfile->isComplete();
    }

    /**
     * Check if user has approved KYC
     */
    public function hasApprovedKyc()
    {
        return $this->kycDocument && $this->kycDocument->isApproved();
    }

    /**
     * Get the registration completion percentage
     */
    public function getRegistrationProgress()
    {
        $steps = 0;
        $completed = 0;

        // Step 1: Basic info (always completed if user exists)
        $steps++;
        $completed++;

        // Step 2: Business profile
        $steps++;
        if ($this->hasCompletedBusinessProfile()) {
            $completed++;
        }

        // Step 3: KYC (optional, but counts if started)
        if ($this->kycDocument) {
            $steps++;
            if ($this->kycDocument->isApproved()) {
                $completed++;
            }
        }

        return [
            'percentage' => $steps > 0 ? round(($completed / $steps) * 100) : 0,
            'completed_steps' => $completed,
            'total_steps' => $steps,
        ];
    }
}
