<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'title',
        'package_amount',
        'interval',
        'user_limit',
        'client_limit',
        'enabled_logged_history',
        'trial_enabled',
        'trial_days',
    ];

    public static $intervals = [
        'Monthly' => 'Monthly',
        'Quarterly' => 'Quarterly',
        'Yearly' => 'Yearly',
        'Unlimited' => 'Unlimited',
    ];

    public static $trialOptions = [
        1 => 'Enabled',
        0 => 'Disabled',
    ];

    public function couponCheck()
    {
       $packages= Coupon::whereRaw("find_in_set($this->id,applicable_packages)")->count();
      return $packages;
    }

    /**
     * Check if trial is enabled for this subscription
     */
    public function hasTrialEnabled()
    {
        return $this->trial_enabled == 1;
    }

    /**
     * Get trial days for this subscription
     */
    public function getTrialDays()
    {
        return $this->hasTrialEnabled() ? $this->trial_days : 0;
    }

    /**
     * Get trial duration in human readable format
     */
    public function getTrialDurationText()
    {
        if (!$this->hasTrialEnabled()) {
            return 'No Trial';
        }
        
        return $this->trial_days . ' ' . ($this->trial_days == 1 ? 'day' : 'days');
    }
}
