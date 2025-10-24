<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class StaffOnboarding extends BaseModel
{
    use HasFactory;

    protected $table = 'staff_onboarding';

    protected $default = [
        'id', 'xid', 'first_name', 'last_name', 'email', 'phone', 
        'position', 'employment_type', 'abn', 'business_name',
        'status', 'admin_notes', 'reviewed_at', 'created_at', 'updated_at',
        'full_name', 'x_reviewed_by',
        // Documents
        'ndis_screening', 'children_check', 'children_check_expiry',
        'passport', 'visa', 'drivers_license', 'ndis_orientation',
        'meals_certificate', 'meals_certificate_expiry',
        'ipc_certificate', 'ipc_certificate_expiry',
        'first_aid_certificate', 'first_aid_expiry',
        'cpr_certificate', 'cpr_expiry',
        'resume', 'staff_contract', 'police_check', 'police_check_expiry',
        // Acknowledgments
        'ack_staff_details', 'ack_induction', 'ack_code_conduct',
        'ack_training_tracker', 'ack_handbook', 'ack_tax_declaration',
        'ack_bank_details', 'final_consent'
    ];

    protected $hidden = ['reviewed_by'];

    protected $appends = ['xid', 'x_reviewed_by', 'full_name'];

    protected $filterable = ['first_name', 'last_name', 'email', 'phone', 'position', 'employment_type', 'status'];

    protected $hashableGetterFunctions = [
        'getXReviewedByAttribute' => 'reviewed_by',
    ];

    protected $fillable = [
        // Personal Information
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'employment_type',
        
        // Contractor Details
        'abn',
        'business_name',
        
        // Documents
        'ndis_screening',
        'children_check',
        'children_check_expiry',
        
        // 100 Points of ID
        'passport',
        'visa',
        'drivers_license',
        
        // Certificates
        'ndis_orientation',
        'meals_certificate',
        'meals_certificate_expiry',
        'ipc_certificate',
        'ipc_certificate_expiry',
        'first_aid_certificate',
        'first_aid_expiry',
        'cpr_certificate',
        'cpr_expiry',
        
        // Other Documents
        'resume',
        'staff_contract',
        'police_check',
        'police_check_expiry',
        
        // Acknowledgments
        'ack_staff_details',
        'ack_induction',
        'ack_code_conduct',
        'ack_training_tracker',
        'ack_handbook',
        'ack_tax_declaration',
        'ack_bank_details',
        'final_consent',
        
        // Status
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'id' => Hash::class . ':staff_onboarding',
        'reviewed_by' => Hash::class . ':user',
        'children_check_expiry' => 'date',
        'meals_certificate_expiry' => 'date',
        'ipc_certificate_expiry' => 'date',
        'first_aid_expiry' => 'date',
        'cpr_expiry' => 'date',
        'police_check_expiry' => 'date',
        'ack_staff_details' => 'boolean',
        'ack_induction' => 'boolean',
        'ack_code_conduct' => 'boolean',
        'ack_training_tracker' => 'boolean',
        'ack_handbook' => 'boolean',
        'ack_tax_declaration' => 'boolean',
        'ack_bank_details' => 'boolean',
        'final_consent' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the reviewer (admin) who reviewed this application.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the hashed reviewed_by ID.
     */
    public function getXReviewedByAttribute()
    {
        return $this->reviewed_by;
    }

    /**
     * Get the full name of the applicant.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if a certificate is expired.
     */
    public function isExpired($certificate)
    {
        $expiryField = $certificate . '_expiry';
        if (!$this->$expiryField) {
            return false;
        }
        return $this->$expiryField < Carbon::today();
    }

    /**
     * Check if a certificate is expiring soon (within 30 days).
     */
    public function isExpiringSoon($certificate)
    {
        $expiryField = $certificate . '_expiry';
        if (!$this->$expiryField) {
            return false;
        }
        return $this->$expiryField <= Carbon::today()->addDays(30) && $this->$expiryField >= Carbon::today();
    }

    /**
     * Get all expired certificates for this application.
     */
    public function getExpiredCertificatesAttribute()
    {
        $certificates = ['children_check', 'meals_certificate', 'ipc_certificate', 'first_aid', 'cpr', 'police_check'];
        $expired = [];
        
        foreach ($certificates as $cert) {
            if ($this->isExpired($cert)) {
                $expired[] = $cert;
            }
        }
        
        return $expired;
    }

    /**
     * Get all certificates expiring soon for this application.
     */
    public function getExpiringSoonCertificatesAttribute()
    {
        $certificates = ['children_check', 'meals_certificate', 'ipc_certificate', 'first_aid', 'cpr', 'police_check'];
        $expiring = [];
        
        foreach ($certificates as $cert) {
            if ($this->isExpiringSoon($cert)) {
                $expiring[] = $cert;
            }
        }
        
        return $expiring;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter approved applications.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}



