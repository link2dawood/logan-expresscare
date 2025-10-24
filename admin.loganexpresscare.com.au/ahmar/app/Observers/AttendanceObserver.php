<?php

namespace App\Observers;

use  App\Models\Attendance;

class AttendanceObserver
{
    public function saving(Attendance $attendance)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $attendance->company_id = $company->id;
        }
    }
}
