<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_type',
        'category_id',
        'subcategory_id',
        'service_country',
        'service_zipcode',
        'service_city',
        'service_address',
        'logo',
        'bio',
        'business_name',
        'business_phone',
        'business_address',
        'website',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    /**
     * Get the user that owns the business profile
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the main category that belongs to the business profile
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    /**
     * Get the subcategory that belongs to the business profile
     */
    public function subcategory()
    {
        return $this->belongsTo(\App\Models\Category::class, 'subcategory_id');
    }

    /**
     * Get the business types available
     */
    public static function getBusinessTypes()
    {
        return [
            'plumbing' => 'Plumbing Services',
            'electrical' => 'Electrical Services',
            'hvac' => 'HVAC Services',
            'cleaning' => 'Cleaning Services',
            'landscaping' => 'Landscaping Services',
            'pest_control' => 'Pest Control',
            'appliance_repair' => 'Appliance Repair',
            'handyman' => 'Handyman Services',
            'roofing' => 'Roofing Services',
            'painting' => 'Painting Services',
            'flooring' => 'Flooring Services',
            'security' => 'Security Services',
            'automotive' => 'Automotive Services',
            'pool_spa' => 'Pool & Spa Services',
            'locksmith' => 'Locksmith Services',
            'moving' => 'Moving Services',
            'carpet_cleaning' => 'Carpet Cleaning',
            'window_cleaning' => 'Window Cleaning',
            'junk_removal' => 'Junk Removal',
            'tree_service' => 'Tree Services',
            'other' => 'Other Services',
        ];
    }

    /**
     * Get the full service location as a formatted string
     */
    public function getFullServiceLocationAttribute()
    {
        $parts = array_filter([
            $this->service_address,
            $this->service_city,
            $this->service_zipcode,
            $this->getCountryName(),
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get country name from country code
     */
    public function getCountryName()
    {
        $countries = [
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
            'AU' => 'Australia',
            'DE' => 'Germany',
            'FR' => 'France',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'CH' => 'Switzerland',
            'AT' => 'Austria',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'IE' => 'Ireland',
            'PT' => 'Portugal',
            'GR' => 'Greece',
            'PL' => 'Poland',
            'CZ' => 'Czech Republic',
            'HU' => 'Hungary',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'HR' => 'Croatia',
            'BG' => 'Bulgaria',
            'RO' => 'Romania',
            'LT' => 'Lithuania',
            'LV' => 'Latvia',
            'EE' => 'Estonia',
            'MT' => 'Malta',
            'CY' => 'Cyprus',
            'LU' => 'Luxembourg',
            'JP' => 'Japan',
            'KR' => 'South Korea',
            'CN' => 'China',
            'IN' => 'India',
            'SG' => 'Singapore',
            'MY' => 'Malaysia',
            'TH' => 'Thailand',
            'ID' => 'Indonesia',
            'PH' => 'Philippines',
            'VN' => 'Vietnam',
            'BD' => 'Bangladesh',
            'PK' => 'Pakistan',
            'LK' => 'Sri Lanka',
            'NP' => 'Nepal',
            'MM' => 'Myanmar',
            'KH' => 'Cambodia',
            'LA' => 'Laos',
            'BN' => 'Brunei',
            'MV' => 'Maldives',
            'BT' => 'Bhutan',
            'MX' => 'Mexico',
            'BR' => 'Brazil',
            'AR' => 'Argentina',
            'CL' => 'Chile',
            'CO' => 'Colombia',
            'PE' => 'Peru',
            'VE' => 'Venezuela',
            'EC' => 'Ecuador',
            'BO' => 'Bolivia',
            'PY' => 'Paraguay',
            'UY' => 'Uruguay',
            'GY' => 'Guyana',
            'SR' => 'Suriname',
            'GF' => 'French Guiana',
            'ZA' => 'South Africa',
            'NG' => 'Nigeria',
            'KE' => 'Kenya',
            'EG' => 'Egypt',
            'MA' => 'Morocco',
            'DZ' => 'Algeria',
            'TN' => 'Tunisia',
            'LY' => 'Libya',
            'SD' => 'Sudan',
            'ET' => 'Ethiopia',
            'UG' => 'Uganda',
            'TZ' => 'Tanzania',
            'RW' => 'Rwanda',
            'BI' => 'Burundi',
            'DJ' => 'Djibouti',
            'SO' => 'Somalia',
            'ER' => 'Eritrea',
        ];
        
        return $countries[$this->service_country] ?? $this->service_country;
    }

    /**
     * Check if the business profile is complete
     */
    public function isComplete()
    {
        return $this->is_completed && 
               !empty($this->business_type) && 
               !empty($this->service_country) &&
               !empty($this->service_city) &&
               !empty($this->bio);
    }

    /**
     * Mark the business profile as completed
     */
    public function markAsCompleted()
    {
        $this->update(['is_completed' => true]);
    }
} 