<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Referral extends BaseModel
{
    protected $table = 'referrals';

    protected $default = ['xid'];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['id'];

    protected $appends = ['xid'];

    protected $filterable = ['first_name', 'last_name', 'email_address', 'phone_number', 'status'];

    protected $hashableGetterFunctions = [];

    protected $casts = [];
}
