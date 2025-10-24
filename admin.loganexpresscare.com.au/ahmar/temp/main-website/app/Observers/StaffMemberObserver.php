<?php

namespace App\Observers;

use App\Classes\Common;
use App\Models\StaffMember;
use Illuminate\Support\Facades\File;

class StaffMemberObserver
{
    public function saving(StaffMember $user)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $user->company_id = $company->id;
        }
    }

    public function updating(StaffMember $user)
    {
        $original = $user->getOriginal();
        if ($user->isDirty('image')) {
            $userImagePath = Common::getFolderPath('userImagePath');

            File::delete($userImagePath . $original['image']);
        }
    }

    public function deleting(StaffMember $user)
    {
        $userImagePath = Common::getFolderPath('userImagePath');

        File::delete($userImagePath . $user->image);
    }
}
