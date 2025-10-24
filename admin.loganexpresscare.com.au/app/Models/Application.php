<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    protected $fillable = [
        'opening_id',
        'submitted_by',
        'applicant_name',
        'contact_email',
        'phone_number',
        'gender',
        'date_of_birth',
        'source',
        'stage',
        'address',
        'cover_letter',
        'image',
        'resume',
        'data_question',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'data_question' => 'array',
    ];

    /**
     * Get the opening/job posting this application is for.
     */
    public function opening()
    {
        return $this->belongsTo(Opening::class, 'opening_id');
    }

    /**
     * Get the user who submitted this application.
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Scope to filter by stage.
     */
    public function scopeStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * Scope to filter by opening.
     */
    public function scopeOpening($query, $openingId)
    {
        return $query->where('opening_id', $openingId);
    }
}









