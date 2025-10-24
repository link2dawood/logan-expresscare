<?php

namespace App\Models;

use App\Traits\EntrustPermissionTrait;

class Permission extends BaseModel
{
    use EntrustPermissionTrait;

    protected $table = 'permissions';

    protected $default = ['xid', 'name', 'display_name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'pivot'];

    protected $appends = ['xid'];
}
