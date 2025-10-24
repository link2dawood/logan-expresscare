<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opening extends Model
{
    use HasFactory;

    protected $table = 'openings';

    protected $fillable = [
        'job_title',
        'description',
        'job_category_id',
        'location_id',
        'publish_date',
        'end_date',
        'active',
        'visible_to',
        'requirements',
        'responsibilities',
        'salary_range',
        'employment_type',
        'experience_level',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean',
    ];

    /**
     * Get the job category for this opening.
     */
    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    /**
     * Get the location for this opening.
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get all applications for this opening.
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'opening_id');
    }

    /**
     * Scope to filter active openings.
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    /**
     * Scope to filter by visibility.
     */
    public function scopeVisibleTo($query, $visibility)
    {
        return $query->where('visible_to', $visibility);
    }
}









