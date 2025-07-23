<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KycDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'document_number',
        'document_front',
        'document_back',
        'document_expiry',
        'business_license',
        'tax_certificate',
        'insurance_certificate',
        'certifications',
        'additional_documents',
        'status',
        'rejection_reason',
        'admin_notes',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'expires_at',
        'terms_accepted',
        'privacy_accepted',
        'ip_address',
    ];

    protected $casts = [
        'certifications' => 'array',
        'additional_documents' => 'array',
        'document_expiry' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
        'terms_accepted' => 'boolean',
        'privacy_accepted' => 'boolean',
    ];

    /**
     * Get the user that owns the KYC document
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who reviewed the KYC
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get available document types
     */
    public static function getDocumentTypes()
    {
        return [
            'passport' => 'Passport',
            'driver_license' => 'Driver\'s License',
            'national_id' => 'National ID Card',
            'voter_id' => 'Voter ID Card',
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions()
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'expired' => 'Expired',
        ];
    }

    /**
     * Check if KYC is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if KYC is pending
     */
    public function isPending()
    {
        return in_array($this->status, ['submitted', 'under_review']);
    }

    /**
     * Check if KYC is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if KYC is expired
     */
    public function isExpired()
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Submit KYC for review
     */
    public function submit()
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Approve KYC
     */
    public function approve($reviewerId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'approved_at' => now(),
            'admin_notes' => $notes,
            'expires_at' => Carbon::now()->addYears(2), // KYC valid for 2 years
        ]);
    }

    /**
     * Reject KYC
     */
    public function reject($reviewerId, $reason, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
            'admin_notes' => $notes,
        ]);
    }
} 