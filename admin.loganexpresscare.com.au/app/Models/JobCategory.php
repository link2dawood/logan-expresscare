<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    use HasFactory;

    protected $table = 'job_categories';

    protected $fillable = [
        'name',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get all openings in this category.
     */
    public function openings()
    {
        return $this->hasMany(Opening::class, 'job_category_id');
    }

    /**
     * Scope to filter active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}









