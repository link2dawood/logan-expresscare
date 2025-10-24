<?php

namespace App\Models;

use App\Casts\Hash;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class News extends BaseModel
{
    protected $table = 'news';

    protected $default = ['xid', 'title', 'description'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    protected $appends = ['xid',];

    protected $filterable = ['title'];


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function newsUser()
    {
        return $this->hasMany(NewsUser::class, 'news_id', 'id');
    }
}
