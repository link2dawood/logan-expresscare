<?php

namespace App\Models;

use App\Casts\Hash;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Feedback extends BaseModel
{
    protected $table = 'feedbacks';

    protected $default = ['xid', 'title', 'description', 'start_date', 'last_date'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'form_id', 'user_id'];

    protected $appends = ['xid', 'x_form_id', 'x_user_id', 'replied', 'not_replied'];

    protected $filterable = ['title'];

    protected $hashableGetterFunctions = [
        'getXFormIdAttribute' => 'form_id',
        'getXUserIdAttribute' => 'user_id',
    ];

    protected $casts = [
        'form_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'feedback_form_fields' => 'array',
        'indicator_fields' => 'array',
        'last_date' => 'datetime',
        'visible_to' => 'integer',
        'rating_details' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function feedbackUser()
    {
        return $this->hasMany(FeedbackUser::class, 'feedback_id', 'id');
    }

    public function getRepliedAttribute()
    {
        $repliedNumber = 0;
        $feedBackId = $this->id;
        $repliedNumber = FeedbackUser::where('feedback_given', 1)
            ->where('feedback_id', $feedBackId)->count();
        return $repliedNumber;
    }

    public function getNotRepliedAttribute()
    {
        $notRepliedNumber = 0;
        $feedBackId = $this->id;
        $notRepliedNumber = FeedbackUser::where('feedback_given', 0)
            ->where('feedback_id', $feedBackId)->count();
        return $notRepliedNumber;
    }

    public function formType()
    {
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }
}
