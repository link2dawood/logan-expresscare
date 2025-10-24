<?php

namespace App\Http\Controllers\Api\Self;

use App\Classes\CommonHrm;
use App\Http\Controllers\ApiBaseController;
use App\Models\Attendance;
use Examyou\RestAPI\ApiResponse;

class AttendantController extends ApiBaseController
{
    protected $model = Attendance::class;

    public function attendanceSummaryByMonth()
    {
        $result = CommonHrm::attendanceDetailByMonth();

        return ApiResponse::make('Data fetched', $result);
    }

    public function attendanceSummary()
    {
        $loggedUser = user();
        $detail = CommonHrm::attendanceDetail($loggedUser->id);

        return ApiResponse::make('Data fetched', [
            'columns' => $detail['columns'],
            'dateRange' => $detail['dateRange'],
            'data' => $detail['data'],
            'meta'  => [
                'paging' => [
                    'total' => $detail['totalRecords']
                ]
            ]
        ]);
    }
}
