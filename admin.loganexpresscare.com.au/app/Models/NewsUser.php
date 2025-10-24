<?php

namespace App\Models;

use App\Casts\Hash;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class NewsUser extends BaseModel
{
    protected $table = 'news_users';

    protected $default = ['xid', 'user_id', 'news_id'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'news_id', 'user_id'];

    protected $appends = ['xid', 'x_news_id', 'x_user_id'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXNewsIdAttribute' => 'news_id',
        'getXUserIdAttribute' => 'user_id',
    ];

    protected $casts = [
        'news_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
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

    public function news()
    {
        return $this->belongsTo(News::class, 'news_id', 'id');
    }
}
