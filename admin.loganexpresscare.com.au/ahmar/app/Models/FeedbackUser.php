<?php

namespace App\Models;

use App\Casts\Hash;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class FeedbackUser extends BaseModel
{
    protected $table = 'feedback_users';

    protected $default = ['xid', 'user_id', 'feedback_id'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'feedback_id', 'user_id'];

    protected $appends = ['xid', 'x_feedback_id', 'x_user_id'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXFeedbackIdAttribute' => 'feedback_id',
        'getXUserIdAttribute' => 'user_id',
    ];

    protected $casts = [
        'feedback_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'data' => 'array',
        'rating_details' => 'array',
        'allowed' => 'integer',
        'feedback_given' => 'integer',
        'submit_date' => 'date',
        'rating' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(StaffMember::class, 'user_id', 'id');
    }

    public function feedback()
    {
        return $this->belongsTo(Feedback::class, 'feedback_id', 'id');
    }
}
