<?php

namespace App\Classes;

use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\Appreciation;
use App\Models\Asset;
use App\Models\StaffMember;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Models\Holiday;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Generate;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\PrePayment;

class RaviCommonHrm
{
    public static function getMinutesFromTimes($startTime, $endTime)
    {

        $timeArray = self::getDetailsArrayFromTimes($startTime, $endTime);
        $isNextDayForTime = self::isTimeForNextDate($startTime, $endTime);

        if ($isNextDayForTime) {
            $totalMinutes = ((24 - $timeArray['start_hours'] - 1) * 60) + (60 - $timeArray['start_minutes']) +  ($timeArray['end_hours'] * 60) +  $timeArray['end_minutes'];
        } else {
            $totalMinutes =  (($timeArray['end_hours'] - $timeArray['start_hours'] - 1) * 60) + (60 - $timeArray['start_minutes']) +  $timeArray['end_minutes'];
        }

        return $totalMinutes;
    }

    public static function createUpdateGenerate($object)
    {
        $request = request();
        $loggedUser = user();

        // Previous no letter head template selected but now selected
        if ($object->getOriginal('letterhead_template_id') == '' && $object->letterhead_template_id != '' && $request->letterhead_description != '') {
            $generate = new Generate();
            $generate->user_id = $object->user_id;
            $generate->letterhead_template_id = $object->letterhead_template_id;
            $generate->title = $request->letterhead_title;
            $generate->description = $request->letterhead_description;
            $generate->admin_id = $loggedUser->id;
            $generate->save();

            $object->generates_id = $generate->id;
        } else if ($object->letterhead_template_id && $request->letterhead_description != '' && $object->generates_id) {
            // Previous letter head template selected and now new one selected
            $generate = Generate::find($object->generates_id);
            $generate->user_id = $object->user_id;
            $generate->letterhead_template_id = $object->letterhead_template_id;
            $generate->title = $request->letterhead_title;
            $generate->description = $request->letterhead_description;
            $generate->save();
        } else if ($object->getOriginal('letterhead_template_id') != '' && $object->letterhead_template_id == '' && $object->generates_id) {
            // Previous letter head template selected and generate exists but now letterhead templated not selected
            Generate::destroy($object->generates_id);
        }

        return $object;
    }

    public static function getDetailsArrayFromTimes($startTime, $endTime)
    {
        $startTimeArray = explode(':', $startTime);
        $endTimeArray = explode(':', $endTime);

        $startTimeHour = $startTimeArray[0];
        $startTimeMinute = $startTimeArray[1];

        $endTimeHour = $endTimeArray[0];
        $endTimeMinute = $endTimeArray[1];

        return [
            'start_hours' => (int) $startTimeHour,
            'start_minutes' => (int) $startTimeMinute,
            'end_hours' => (int) $endTimeHour,
            'end_minutes' => (int) $endTimeMinute,
        ];
    }

    public static function updateAccountAmount($id)
    {

        $totalPrePayment = PrePayment::where('account_id', $id)->sum('amount');

        $totalDeposite = Deposit::where('account_id', $id)->sum('amount');

        $totalPayRoll = Payroll::where('account_id', $id)->where('status', 'paid')->sum('net_salary');

        $totalExpense = Expense::where('account_id', $id)->where('status', 'approved')
            ->sum('amount');

        $totalAsset = Asset::where('account_id', $id)
            ->sum('price');

        $totalAppreciation = Appreciation::where('account_id', $id)
            ->sum('price_amount');

        $totalAccountSum = Account::where('accounts.id', $id)
            ->sum('initial_balance');


        // Calculate the final total amount
        $totalAmount =  $totalAccountSum - $totalExpense - $totalPayRoll +  $totalDeposite - $totalPrePayment - $totalAsset - $totalAppreciation;
        $AccountUpdate = Account::find($id);

        if ($AccountUpdate) {
            $AccountUpdate->balance = $totalAmount;
            $AccountUpdate->save();
        }
    }

    public static function insertAccountEntries($accountId, $oldAccountId, $type, $date, $id, $amount)
    {
        if ($type == 'payroll') {
            $accountEntries = AccountEntry::where('account_id', $accountId)->where('type', $type)->where('payroll_id', $id)->first();
            if (!$accountEntries) {
                $accountEntries = new AccountEntry();
            };
            $accountEntries->payroll_id = $id;
            $accountEntries->is_debit = 1;
        } else if ($type == 'pre_payment') {
            $accountEntries = AccountEntry::where('type', $type)->where('pre_payment_id', $id)->first();
            if (!$accountEntries) {
                $accountEntries = new AccountEntry();
            };
            $accountEntries->pre_payment_id = $id;
            $accountEntries->is_debit = 1;
        } else if ($type == 'expense') {
            $accountEntries = AccountEntry::where('type', $type)->where('expense_id', $id)->first();
            if (!$accountEntries) {
                $accountEntries = new AccountEntry();
            };
            $accountEntries->expense_id = $id;
            $accountEntries->is_debit = 1;
        } else if ($type == 'asset') {
            if ($oldAccountId == null) {
                $accountEntries = new AccountEntry();
            } else {
                $accountEntries = AccountEntry::where('account_id', $oldAccountId)->where('type', $type)->where('asset_id', $id)->first();
            };
            $accountEntries->asset_id = $id;
            $accountEntries->is_debit = 1;
        } else if ($type == 'appreciation') {
            if ($oldAccountId == null) {
                $accountEntries = new AccountEntry();
            } else {
                $accountEntries = AccountEntry::where('account_id', $oldAccountId)->where('type', $type)->where('appreciation_id', $id)->first();
            };
            $accountEntries->appreciation_id = $id;
            $accountEntries->is_debit = 1;
        } else if ($type == 'deposit') {
            if ($oldAccountId == null) {
                $accountEntries = new AccountEntry();
            } else {
                $accountEntries = AccountEntry::where('account_id', $oldAccountId)->where('type', $type)->where('deposit_id', $id)->first();
            };
            $accountEntries->deposit_id = $id;
            $accountEntries->is_debit = 0;
        } else if ($type == 'initial_balance') {
            $accountEntries = AccountEntry::where('account_id', $accountId)->where('type', $type)->where('initial_account_id', $id)->first();
            if (!$accountEntries) {
                $accountEntries = new AccountEntry();
            };
            $accountEntries->initial_account_id = $id;
            $accountEntries->is_debit = 0;
        }
        $accountEntries->account_id = $accountId;
        $accountEntries->date = $date;
        $accountEntries->type = $type;
        $accountEntries->amount = $amount;
        $accountEntries->save();
    }

    public static function isTimeForNextDate($startTime, $endTime)
    {
        $timeArray = self::getDetailsArrayFromTimes($startTime, $endTime);

        //for 24 hours shift we have change below condition from > to >=

        return $timeArray['start_hours'] >= $timeArray['end_hours'] ? true : false;
    }

    public static function isLateClockedIn($officeStartTime, $clockInTime, $currentDate, $actualDate)
    {
        $isLate = false;

        // here $currendDate is the actual clocking date while $actualDate is date of attendance

        $timeArray = self::getDetailsArrayFromTimes($officeStartTime, $clockInTime);

        if ($timeArray['end_hours'] > $timeArray['start_hours']) {
            $isLate = true;
        } else if ($timeArray['end_hours'] == $timeArray['start_hours']) {
            $isLate =  $timeArray['end_minutes'] <= $timeArray['start_minutes'] ? false : true;
        }

        if ($currentDate && $currentDate != null) {
            $current = Carbon::parse($currentDate)->toDateString();
            $actual = Carbon::parse($actualDate)->toDateString();

            if ($current > $actual) {
                $isLate = true;
            };
        }

        return $isLate;
    }

    public static function getUserClockingTime($userId)
    {
        $company = company();

        $user = StaffMember::select('id', 'name', 'shift_id')->with(['shift'])
            ->where('company_id', $company->id)
            ->find($userId);

        $allIps = [];
        $allowedIpAddress = $user && $user->shift ? $user->shift->allowed_ip_address : $company->allowed_ip_address;
        if ($allowedIpAddress) {
            foreach ($allowedIpAddress as $allIpAddress) {
                $allIps[] = $allIpAddress['allowed_ip_address'];
            }
        }

        $clockInTime = $user && $user->shift ? $user->shift->clock_in_time : $company->clock_in_time;
        $clockOutTime = $user && $user->shift ? $user->shift->clock_out_time : $company->clock_out_time;
        if (!$clockInTime) {
            $clockInTime = "09:30:00";
        }
        if (!$clockOutTime) {
            $clockOutTime = "18:00:00";
        }

        $earlyClockInTime = $user && $user->shift ? $user->shift->early_clock_in_time : $company->early_clock_in_time;
        if ($earlyClockInTime) {
            $actualAllowedClockIn =  Carbon::createFromFormat('H:i:s', $clockInTime)->subMinutes($earlyClockInTime)->format('H:i:s');
        } else {
            $actualAllowedClockIn = $clockInTime;
        }
        $earlyClockOutTime = $user && $user->shift ? $user->shift->allow_clock_out_till : $company->allow_clock_out_till;
        if ($earlyClockOutTime) {
            $actualAllowedClockOut =  Carbon::createFromFormat('H:i:s', $clockOutTime)->addMinutes($earlyClockOutTime)->format('H:i:s');
        } else {
            $actualAllowedClockOut = $clockOutTime;
        }

        // If user have shift then shift time will be return
        // Else company time will be return
        return [
            'clock_in_time' => $clockInTime,
            'clock_out_time' => $clockOutTime,
            'early_clock_in_time' => $user && $user->shift ? $user->shift->early_clock_in_time : $company->early_clock_in_time,
            'allow_clock_out_till' => $user && $user->shift ? $user->shift->allow_clock_out_till : $company->allow_clock_out_till,
            'late_mark_after' => $user && $user->shift ? $user->shift->late_mark_after : $company->late_mark_after,
            'self_clocking' => $user && $user->shift ? $user->shift->self_clocking : $company->self_clocking,
            'allowed_ip_address' => $allIps,
            'actual_allowed_clock_in_time' => $actualAllowedClockIn,
            'actual_allowed_clock_out_time' => $actualAllowedClockOut,
            'is_next_day' => $user && $user->shift ? $user->shift->is_next_day : 0,
        ];
    }

    public static function getShiftTimeFromDate($date, $userId)
    {
        $company = company();
        $clockTiming = self::getUserClockingTime($userId);

        $clockInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockTiming['clock_in_time'], $company->timezone)
            ->setTimezone('UTC');
        $clockOutDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockTiming['clock_out_time'], $company->timezone)
            ->setTimezone('UTC');

        return [
            'clock_in_date_time' => $clockInDateTime,
            'clock_out_date_time' => $clockOutDateTime,
        ];
    }

    public static function getFincialYearStartEndDate($year)
    {
        $company = company();
        $startMonth = (int)$company->leave_start_month;

        $startDate = Carbon::create($year, $startMonth, 1, 0, 0, 0)->setTimezone('UTC')->startOfDay();
        $endDate = $startDate->copy()->addYear()->subDay();

        return [
            'startDate' => $startDate,
            'endDate'   => $endDate
        ];
    }

    public static function isPaidLeaveOrNot($date, $userId, $leaveTypeId)
    {
        $isPaid = true;

        // Check if the date is in the holidays table
        $isHoliday = Holiday::whereDate('date', $date)->exists();

        $leaveType = LeaveType::find($leaveTypeId);
        $maxLeavePerMonth = $leaveType->max_leaves_per_month;

        // Get financial year
        $financialYear = self::getFincialYearFromDate($date);

        // Get month and total leave stats
        $dateDetails = self::getDateDetails($date);
        $leaveMonth = $dateDetails['month'];
        $paidLeaveTakenThisMonth = self::totalPaidLeavesByYearMonth($leaveTypeId, $userId, $financialYear, $leaveMonth);
        $totalLeaveTakenThisYear = self::totalPaidLeavesByYear($leaveTypeId, $userId, $financialYear);

        // Determine if paid leave is allowed
        if (!$isHoliday) {
            if (
                $totalLeaveTakenThisYear >= $leaveType->total_leaves ||
                ($maxLeavePerMonth !== null && $paidLeaveTakenThisMonth >= $maxLeavePerMonth)
            ) {
                $isPaid = false;
            }
        }

        // Force unpaid if leave type is unpaid
        $isPaid = $leaveType->is_paid == 0 ? 0 : $isPaid;

        return [
            'isHoliday' => $isHoliday,
            'isPaid'   => $isPaid,
            'paidLeaveTakenThisMonth' => $paidLeaveTakenThisMonth,
            'totalLeaveTakenThisYear' => $totalLeaveTakenThisYear,
            'totalLeaves' => $leaveType->total_leaves,
            'maxLeavePerMonth' => $maxLeavePerMonth,
        ];
    }


    public static function getDateDetails($date)
    {
        $dateArray = explode('-', $date);

        return [
            'year' => $dateArray[0],
            'month' => $dateArray[1],
            'day' => $dateArray[2],
        ];
    }

    // Get Fincial Year from a date
    public static function getFincialYearFromDate($date)
    {
        $dateDetails = self::getDateDetails($date);
        $company = company();
        $dateYear = $dateDetails['year'];
        $startMonth = (int)$company->leave_start_month;

        // Set Date as Date Object
        $dateObject = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00', $company->timezone);
        $startDate = Carbon::create($dateYear, $startMonth, 1)->setTimezone($company->timezone)->startOfDay();
        $endDate = $startDate->copy()->addYear()->subDay();

        // If current
        if (!$dateObject->between($startDate, $endDate)) {
            $dateYear -= 1;
        }

        return $dateYear;
    }

    public static function totalPaidLeavesByYearMonth($leaveTypeId, $userId, $year, $month)
    {
        $totalFullDayLeavesCount = Attendance::join('leaves', 'leaves.id', '=', 'attendances.leave_id')
            ->whereNotNull('attendances.leave_id')
            ->whereYear('attendances.date', $year)
            ->whereMonth('attendances.date', $month)
            ->where('leaves.leave_type_id', $leaveTypeId)
            ->where('attendances.user_id', $userId)
            ->where('attendances.is_half_day', 0)
            ->where('attendances.is_paid', 1)
            ->count();

        $totalHalfDayLeavesCount = Attendance::join('leaves', 'leaves.id', '=', 'attendances.leave_id')
            ->whereNotNull('attendances.leave_id')
            ->whereYear('attendances.date', $year)
            ->whereMonth('attendances.date', $month)
            ->where('leaves.leave_type_id', $leaveTypeId)
            ->where('attendances.user_id', $userId)
            ->where('attendances.is_half_day', 1)
            ->where('attendances.is_paid', 1)
            ->count();

        $totalLeaves = ($totalHalfDayLeavesCount / 2) + $totalFullDayLeavesCount;

        return $totalLeaves;
    }

    public static function totalPaidLeavesByYear($leaveTypeId, $userId, $year)
    {
        $fincialDates = self::getFincialYearStartEndDate($year);
        $startDate = $fincialDates['startDate'];
        $endDate = $fincialDates['endDate'];

        $totalFullDayLeavesCount = Attendance::join('leaves', 'leaves.id', '=', 'attendances.leave_id')
            ->whereNotNull('attendances.leave_id')
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->where('leaves.leave_type_id', $leaveTypeId)
            ->where('attendances.user_id', $userId)
            ->where('attendances.is_half_day', 0)
            ->where('attendances.is_paid', 1)
            ->count();

        $totalHalfDayLeavesCount = Attendance::join('leaves', 'leaves.id', '=', 'attendances.leave_id')
            ->whereNotNull('attendances.leave_id')
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->where('leaves.leave_type_id', $leaveTypeId)
            ->where('attendances.user_id', $userId)
            ->where('attendances.is_half_day', 1)
            ->where('attendances.is_paid', 1)
            ->count();

        $totalLeaves = ($totalHalfDayLeavesCount / 2) + $totalFullDayLeavesCount;

        return $totalLeaves;
    }

    public static function checkIfAttendanceAlreadyExists($userId, $startDate, $endDate = null)
    {
        if ($endDate != null) {
            $allDates = CarbonPeriod::create($startDate, $endDate);

            foreach ($allDates as $allDate) {
                $attendaceCount = Attendance::where('user_id', $userId)->whereDate('date', $allDate->format("Y-m-d"))->count();

                if ($attendaceCount > 0) {
                    throw new ApiException("Attendance already exists for date " . $allDate->format("Y-m-d"));
                }
            }
        } else {
            $attendaceCount = Attendance::where('user_id', $userId)->whereDate('date', $startDate)->count();

            if ($attendaceCount > 0) {
                throw new ApiException("Attendance already exists for date " . $startDate);
            }
        }
    }

    public static function getIpAddress()
    {
        $ipaddress = '';

        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    public static function getTodayAttendanceDetails()
    {
        $request = request();
        $company = company();
        $user = user();
        $shiftClockInTime = self::getUserClockingTime($user->id);


        $latestAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();
        $isClockedIN = $latestAttendance->clock_in_time;
        $isClockedOUT = $latestAttendance->clock_out_time;

        if (strtotime($shiftClockInTime['clock_out_time']) <= strtotime($shiftClockInTime['clock_in_time'])) {
            $actualClockingTime = Carbon::now($company->timezone)->format('H:i:s');
            if (strtotime($actualClockingTime) < strtotime($shiftClockInTime['actual_allowed_clock_in_time'])) {

                //if a person comes after midnight (after 00:00:00:am, next day) then we consider the perious date

                $currentDateTimeObject = Carbon::now($company->timezone)->subDay();
            } else {
                $currentDateTimeObject = Carbon::now($company->timezone);
            }
        } else {
            $currentDateTimeObject = Carbon::now($company->timezone);
        }


        $currentTime = $currentDateTimeObject->copy()->format('H:i:s');
        $currentDate = $currentDateTimeObject->copy()->format('Y-m-d');

        // to display holiday message on dashboard

        $todayIsHoliday = false;
        $showClockedInButton = false;
        $showClockedOutButton = false;
        $officeHoursExpired = false;

        $totalMinutesOfShift = self::getMinutesFromTimes(
            $shiftClockInTime['clock_in_time'],
            $shiftClockInTime['clock_out_time']
        );

        $earlyClockInMinutes = $shiftClockInTime && $shiftClockInTime['early_clock_in_time'] ? $shiftClockInTime['early_clock_in_time'] : 0;
        $clockOutAfterInMinutes = $shiftClockInTime && $shiftClockInTime['allow_clock_out_till'] ? $shiftClockInTime['allow_clock_out_till'] : 0;

        $leave = Leave::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->where('status', 'approved')
            ->where('user_id', $user->id)->first();
        // dd($leave);
        $holiday = Holiday::where('date', $currentDate)->first();

        if (strtotime($shiftClockInTime['clock_out_time']) <= strtotime($shiftClockInTime['clock_in_time'])) {
            $actualClockingTime = Carbon::now($company->timezone)->format('H:i:s');

            if (strtotime($shiftClockInTime['actual_allowed_clock_in_time']) <= strtotime($actualClockingTime)) {
                // like after 21:45:00pm to 23:59:59....
                $nextDate = Carbon::now($company->timezone)->format('Y-m-d');
                $actualAllowed = $nextDate . ' ' . $shiftClockInTime['actual_allowed_clock_in_time']; // Next day timestamp
                // leave according time...
                $leave = Leave::where('start_date', '<=', $nextDate)
                    ->where('end_date', '>=', $nextDate)->where('status', 'approved')
                    ->where('user_id', $user->id)->first();
                $holiday = Holiday::where('date', $nextDate)->first();
            } else {
                // like 00:00:00 to 22:00:00...

                if (strtotime($actualClockingTime) <= strtotime($shiftClockInTime['actual_allowed_clock_out_time'])) {
                    $yesterday = Carbon::now($company->timezone)->subDay()->format('Y-m-d');
                    $actualAllowed = $yesterday . ' ' . $shiftClockInTime['actual_allowed_clock_in_time'];
                    // leave according time...
                    $leave = Leave::where('start_date', '<=', $yesterday)
                        ->where('end_date', '>=', $yesterday)->where('status', 'approved')
                        ->where('user_id', $user->id)->first();
                    $holiday = Holiday::where('date', $yesterday)->first();
                } else {
                    $today = Carbon::now($company->timezone)->format('Y-m-d');
                    $actualAllowed = $today . ' ' . $shiftClockInTime['actual_allowed_clock_in_time'];
                    // leave according time...
                    $leave = Leave::where('start_date', '<=', $today)
                        ->where('end_date', '>=', $today)->where('status', 'approved')
                        ->where('user_id', $user->id)->first();
                    $holiday = Holiday::where('date', $today)->first();
                }
            }


            $presentDateTime = Carbon::now($company->timezone)->format('Y-m-d H:i:s');

            if (strtotime($presentDateTime) < strtotime($actualAllowed)) {

                $showClockedInButton = false;
                $showClockedOutButton = false;

                $officeHoursExpired = true;
            } else {
                $showClockedInButton = true;
                $showClockedOutButton = true;
                $officeHoursExpired = false;
            }
        } else {
            // Early Office Start Time
            $earlyOfficeStartTime = Carbon::createFromFormat('Y-m-d H:i:s', $currentDate . ' ' . $shiftClockInTime['clock_in_time'], $company->timezone)
                ->subMinutes($earlyClockInMinutes);

            // Office hours passed
            $maxOfficeEndTime = Carbon::createFromFormat('Y-m-d H:i:s', $currentDate . ' ' . $shiftClockInTime['clock_out_time'], $company->timezone)
                ->addMinutes($clockOutAfterInMinutes);

            // If current time is greater than office early time
            // Then show clock in button
            if ($currentDateTimeObject->copy()->gte($earlyOfficeStartTime)) {
                $showClockedInButton = true;
            }

            // If current time is greate than max time of office
            // It mean office hours passed and cannot login and logout
            if (
                $currentDateTimeObject->copy()->gte($earlyOfficeStartTime) &&
                $currentDateTimeObject->copy()->lte($maxOfficeEndTime)
            ) {
                $showClockedInButton = true;
                $showClockedOutButton = true;
            } else {

                $showClockedInButton = false;
                $showClockedOutButton = false;

                $officeHoursExpired = true;
            }
        }

        if ($holiday) {
            if ($holiday->is_half_day == 1) {

                $beforehalfMinutes = ($totalMinutesOfShift / 2) + ($shiftClockInTime['allow_clock_out_till']);

                $actualAllowedLeaveClockOut =  Carbon::createFromFormat('H:i:s', $shiftClockInTime['clock_in_time'])
                    ->addMinutes($beforehalfMinutes)->format('H:i:s');

                $afterhalfMinutes = ($totalMinutesOfShift / 2) - ($shiftClockInTime['early_clock_in_time']);

                $actualAllowedAfterBreakIn =  Carbon::createFromFormat('H:i:s', $shiftClockInTime['clock_in_time'])
                    ->addMinutes($afterhalfMinutes)->format('H:i:s');

                if ($holiday->half_holiday == 'before_break') {

                    if (strtotime($shiftClockInTime['clock_out_time']) < strtotime($shiftClockInTime['clock_in_time'])) {

                        // If shift crosses midnight, ensure proper date handling
                        if (strtotime($actualAllowedAfterBreakIn) >= strtotime($currentTime)) {
                            $nextDate = Carbon::now($company->timezone)->format('Y-m-d');
                            $actualAllowed = $nextDate . ' ' . $actualAllowedAfterBreakIn; // Next day timestamp

                        } else {
                            $actualAllowed = $currentDate . ' ' . '23:59:59'; // Same day timestamp
                        }

                        $presentDateTime = Carbon::now($company->timezone)->format('Y-m-d H:i:s');

                        if (strtotime($presentDateTime) < strtotime($actualAllowed)) {

                            $todayIsHoliday = true;
                        } else {
                            $todayIsHoliday = false;
                        }
                    } else {

                        if (
                            $currentTime >= $shiftClockInTime['actual_allowed_clock_in_time'] &&
                            $currentTime <= $actualAllowedAfterBreakIn
                        ) {
                            $todayIsHoliday = true;
                        } else {
                            $todayIsHoliday = false;
                        }
                    }
                } else {

                    if (strtotime($shiftClockInTime['clock_out_time']) < strtotime($shiftClockInTime['clock_in_time'])) {

                        // If shift crosses midnight, ensure proper date handling

                        if (strtotime($actualAllowedLeaveClockOut) >= strtotime($currentTime)) {
                            $nextDate = Carbon::now($company->timezone)->format('Y-m-d');
                            $actualAllowed = $nextDate . ' ' . $actualAllowedLeaveClockOut; // Next day timestamp

                        } else {
                            // $actualAllowed = $currentDate . ' ' . '23:59:59'; // Same day timestamp
                            if (strtotime($currentTime) <= strtotime($shiftClockInTime['actual_allowed_clock_out_time'])) {
                                $yesterday = Carbon::now($company->timezone)->subDay()->format('Y-m-d');
                                $actualAllowed = $yesterday . ' ' . $shiftClockInTime['actual_allowed_clock_in_time'];
                            } else {
                                $tomorrow = Carbon::now($company->timezone)->addDay()->format('Y-m-d');
                                $actualAllowed = $tomorrow . ' ' . $shiftClockInTime['actual_allowed_clock_in_time'];
                            }
                        }
                        $presentDateTime = Carbon::now($company->timezone)->format('Y-m-d H:i:s');

                        if (strtotime($presentDateTime) > strtotime($actualAllowed)) {

                            $todayIsHoliday = true;
                        } else {
                            $todayIsHoliday = false;
                        }
                    } else {

                        if (
                            strtotime($currentTime) >= strtotime($actualAllowedLeaveClockOut) &&
                            strtotime($currentTime) <= strtotime($shiftClockInTime['actual_allowed_clock_out_time'])
                        ) {
                            $todayIsHoliday = true;
                        } else {
                            $todayIsHoliday = false;
                        }
                    }
                }
            } else {
                $todayIsHoliday = true;
            }
        }

        // to display leave message on deshboard

        $isOnFullDayLeave = false;

        if ($leave) {
            if ($leave->is_half_day == 1) {

                $beforehalfMinutes = ($totalMinutesOfShift / 2) + ($shiftClockInTime['allow_clock_out_till']);

                $actualAllowedLeaveClockOut =  Carbon::createFromFormat('H:i:s', $shiftClockInTime['clock_in_time'])
                    ->addMinutes($beforehalfMinutes)->format('H:i:s');

                $afterhalfMinutes = ($totalMinutesOfShift / 2) - ($shiftClockInTime['early_clock_in_time']);

                $actualAllowedAfterBreakIn =  Carbon::createFromFormat('H:i:s', $shiftClockInTime['clock_in_time'])
                    ->addMinutes($afterhalfMinutes)->format('H:i:s');

                if ($leave->half_leave == 'before_break') {

                    if (strtotime($shiftClockInTime['clock_out_time']) < strtotime($shiftClockInTime['clock_in_time'])) {

                        // If shift crosses midnight, ensure proper date handling
                        if (strtotime($actualAllowedAfterBreakIn) >= strtotime($currentTime)) {
                            $nextDate = Carbon::now($company->timezone)->format('Y-m-d');
                            $actualAllowed = $nextDate . ' ' . $actualAllowedAfterBreakIn; // Next day timestamp

                        } else {
                            $actualAllowed = $currentDate . ' ' . '23:59:59'; // Same day
                        }

                        $presentDateTime = Carbon::now($company->timezone)->format('Y-m-d H:i:s');
                        if (strtotime($presentDateTime) < strtotime($actualAllowed)) {

                            $isOnFullDayLeave = true;
                        } else {
                            $isOnFullDayLeave = false;
                            $showClockedInButton = true;
                            $showClockedOutButton = true;
                        }
                    } else {
                        if (
                            $currentTime >= $shiftClockInTime['actual_allowed_clock_in_time'] &&
                            $currentTime <= $actualAllowedAfterBreakIn
                        ) {
                            $isOnFullDayLeave = true;
                        } else {
                            $isOnFullDayLeave = false;
                            $showClockedInButton = true;
                            $showClockedOutButton = true;
                        }
                    }
                } else {

                    if (strtotime($shiftClockInTime['clock_out_time']) < strtotime($shiftClockInTime['clock_in_time'])) {

                        // If shift crosses midnight, ensure proper date handling
                        // dd(strtotime($actualAllowedLeaveClockOut) >= strtotime($currentTime));
                        if (strtotime($actualAllowedLeaveClockOut) >= strtotime($currentTime)) {
                            $nextDate = Carbon::now($company->timezone)->format('Y-m-d');
                            $actualAllowed = $nextDate . ' ' . $actualAllowedLeaveClockOut; // Next day timestamp

                        } else {

                            if (strtotime($currentTime) <= strtotime($shiftClockInTime['actual_allowed_clock_out_time'])) {
                                $yesterday = Carbon::now($company->timezone)->subDay()->format('Y-m-d');
                                $actualAllowed = $yesterday . ' ' . $shiftClockInTime['actual_allowed_clock_in_time'];
                            } else {
                                $tomorrow = Carbon::now($company->timezone)->addDay()->format('Y-m-d');
                                $actualAllowed = $tomorrow . ' ' . $actualAllowedLeaveClockOut;
                            }
                        }
                        $presentDateTime = Carbon::now($company->timezone)->format('Y-m-d H:i:s');

                        if (strtotime($presentDateTime) > strtotime($actualAllowed)) {
                            $isOnFullDayLeave = true;
                        } else {
                            $isOnFullDayLeave = false;
                        }
                    } else {

                        if (
                            strtotime($currentTime) >= strtotime($actualAllowedLeaveClockOut) &&
                            strtotime($currentTime) <= strtotime($shiftClockInTime['actual_allowed_clock_out_time'])
                        ) {
                            $isOnFullDayLeave = true;
                        } else {
                            $isOnFullDayLeave = false;
                            $showClockedInButton = true;
                            $showClockedOutButton = true;
                        }
                    }
                }
            } else {
                $isOnFullDayLeave = true;
            }
        }

        $isUserAttendanceExists = Attendance::whereDate('attendances.date', $currentDate)
            ->where('attendances.user_id', $user->id)
            ->first();

        $isClockedIn = false;
        $isClockedOut = false;
        $clockInTime = null;
        $clockInDateTime = null;
        $clockOutTime = null;
        $clockOutDateTime = null;
        if ($isUserAttendanceExists) {

            if ($isUserAttendanceExists->is_leave && $isUserAttendanceExists->is_half_day == 0) {
                $showClockedInButton = false;
                $showClockedOutButton = false;

                $isOnFullDayLeave = true;
            } else {

                // If user clocked in then don't show clock in button
                if ($isUserAttendanceExists->clock_in_time) {
                    $isClockedIn = true;
                    $showClockedInButton = false;
                }

                if ($isUserAttendanceExists->clock_out_time) {
                    $isClockedOut = true;
                    $showClockedOutButton = false;
                }
            }

            $clockInTime = $isUserAttendanceExists->clock_in_time;
            $clockInDateTime = $isUserAttendanceExists->clock_in_date_time;
            $clockOutTime = $isUserAttendanceExists->clock_out_time;
            $clockOutDateTime = $isUserAttendanceExists->clock_out_date_time;
        }

        return [
            'date' => $currentDate,
            'time' => $currentTime,
            'is_on_full_day_leave' => $isOnFullDayLeave,
            'is_clocked_in' => $isClockedIn,
            'is_clocked_out' => $isClockedOut,
            'office_hours_expired' => $officeHoursExpired,
            'clock_in_time' => $clockInTime,
            'clock_in_date_time' => $clockInDateTime,
            'clock_out_time' => $clockOutTime,
            'clock_out_date_time' => $clockOutDateTime,
            'show_clock_in_button' => $showClockedInButton,
            'show_clock_out_button' => $showClockedOutButton,
            'today_is_holiday' => $todayIsHoliday,
            'office_clock_in_time' => $shiftClockInTime['clock_in_time'],
            'office_clock_out_time' => $shiftClockInTime['clock_out_time'],
            'self_clocking' => $shiftClockInTime['self_clocking'],
        ];
    }

    public static function getMonthYearAttendanceDetails($userId, $month, $year)
    {
        $company = company();
        $user = StaffMember::select('id', 'name', 'shift_id')->with(['shift'])->find($userId);

        $currentDateTime = Carbon::now($company->timezone);
        $today = Carbon::now($company->timezone)->startOfDay();
        $startDate = Carbon::createFromDate($year, $month, 1, $company->timezone)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->startOfDay();

        $attendanceData = [];
        $lateTime = 0;
        $clockedInDuration = 0;
        $totalLateDays = 0;
        $totalHalfDays = 0;
        $totalPaidLeave = 0;
        $paidLeaveCount = 0;
        $totalUnPaidLeave = 0;
        $totalHolidayCount = 0;
        $totalDays = 0;

        $shiftDetails = self::getUserClockingTime($userId);

        $shiftClockInTime = $shiftDetails['clock_in_time'];
        $shiftClockOutTime = $shiftDetails['clock_out_time'];

        $officeHoursInMinutes = self::getMinutesFromTimes($shiftClockInTime, $shiftClockOutTime);

        $allAttendances = Attendance::with(['leave:id,leave_type_id,reason', 'leave.leaveType:id,name', 'holiday'])
            ->whereBetween('attendances.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('attendances.user_id', $userId)
            ->get();

        $allHolidays = Holiday::whereBetween('holidays.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $totalWorkingDays = 0;
        $totalPresentDays = 0;

        while ($endDate->gte($startDate)) {
            $currentDate = $endDate->copy();

            $isAttendanceDataFound = $allAttendances->firstWhere(fn($a) => $a->date->format('Y-m-d') === $currentDate->format('Y-m-d'));
            $isHolidayDataFound = $allHolidays->firstWhere(fn($h) => $h->date->format('Y-m-d') === $currentDate->format('Y-m-d'));

            $totalWorkDurationInMinutes = 0;
            $isHoliday = false;
            $isHalfHoliday = false;
            $isLeave = false;
            $holidayName = '';
            $leaveName = '';
            $leaveReason = '';
            $status = 'not_marked';

            if ($isHolidayDataFound && $isHolidayDataFound->is_half_day == 1) {
                $isHalfHoliday = true;
                $holidayName = $isHolidayDataFound->name;
            }

            if ($currentDate->gt($today)) {
                $status = 'upcoming';
            } else if ($isAttendanceDataFound) {
                if ($isAttendanceDataFound->leave_type_id && $isAttendanceDataFound->is_half_day) {
                    $status = 'half_day';
                    $isLeave = true;
                    $leaveName = optional(optional($isAttendanceDataFound->leave)->leaveType)->name;
                    $leaveReason = optional($isAttendanceDataFound->leave)->reason;

                    $totalPresentDays += 0.5;
                    $totalHalfDays += 1;
                } else if ($isAttendanceDataFound->leave_type_id) {
                    $status = 'absent';
                    $isLeave = true;
                    $leaveName = optional(optional($isAttendanceDataFound->leave)->leaveType)->name;
                    $leaveReason = optional($isAttendanceDataFound->leave)->reason;
                } else if ($isHalfHoliday) {
                    $status = 'half_holiday';
                    $isHoliday = true;
                    $totalHolidayCount += 0.5;
                    $totalPresentDays += 1;
                } else {
                    $status = 'present';
                    $totalPresentDays += 1;
                }

                if (in_array($status, ['half_day', 'present', 'half_holiday'])) {
                    if ($isAttendanceDataFound->clock_in_time) {
                        $clockInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $today->format('Y-m-d') . ' ' . $isAttendanceDataFound->clock_in_time);

                        if ($isAttendanceDataFound->clock_out_time) {
                            $clockOutDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $today->format('Y-m-d') . ' ' . $isAttendanceDataFound->clock_out_time);
                            $totalWorkDurationInMinutes = $clockOutDateTime->diffInMinutes($clockInDateTime);

                            if (strtotime($isAttendanceDataFound->office_clock_out_time) < strtotime($isAttendanceDataFound->office_clock_in_time)) {
                                if (strtotime($isAttendanceDataFound->clock_in_time) > strtotime($isAttendanceDataFound->clock_out_time)) {
                                    $totalWorkDurationInMinutes = self::getMinutesFromTimes($isAttendanceDataFound->clock_in_time, $isAttendanceDataFound->clock_out_time);
                                }
                            }
                        } elseif ($user && $user->shift) {
                            $shiftClockOutDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $today->format('Y-m-d') . ' ' . $user->shift->clock_out_time);
                            $totalWorkDurationInMinutes = $shiftClockOutDateTime->diffInMinutes($clockInDateTime);

                            if (strtotime($user->shift->clock_out_time) < strtotime($user->shift->clock_in_time)) {
                                $totalWorkDurationInMinutes = self::getMinutesFromTimes($isAttendanceDataFound->clock_in_time, $user->shift->clock_out_time);
                            }
                        } elseif (is_null($isAttendanceDataFound->clock_out_date_time)) {
                            $totalWorkDurationInMinutes = $currentDateTime->diffInMinutes($clockInDateTime);
                        }
                    }

                    if ($isAttendanceDataFound->is_late) {
                        $isLateClockedIn = CommonHrm::isLateClockedIn(
                            $isAttendanceDataFound->office_clock_in_time,
                            $isAttendanceDataFound->clock_in_time,
                            $isAttendanceDataFound->actual_clock_in_date,
                            $isAttendanceDataFound->date
                        );

                        $lateTime = $isLateClockedIn
                            ? CommonHrm::getMinutesFromTimes($isAttendanceDataFound->office_clock_in_time, $isAttendanceDataFound->clock_in_time)
                            : 0;
                    } else {
                        $lateTime = 0;
                    }
                }

                if ($isAttendanceDataFound->is_paid) {
                    $totalPaidLeave += $isAttendanceDataFound->is_half_day ? 0.5 : 1;
                    if ($isAttendanceDataFound->leave_type_id) {
                        $paidLeaveCount += $isAttendanceDataFound->is_half_day ? 0.5 : 1;
                    }
                } else {
                    $totalUnPaidLeave += $isAttendanceDataFound->is_half_day ? 0.5 : 1;
                }

                $totalWorkingDays += $isHalfHoliday ? 0.5 : 1;
            } else if ($isHolidayDataFound && !$isAttendanceDataFound) {
                if ($isHalfHoliday) {
                    $status = 'half_holiday';
                    $isHoliday = true;
                    $totalHolidayCount += 0.5;
                    $totalWorkingDays += 0.5;
                } else {
                    $status = 'holiday';
                    $isHoliday = true;
                    $totalHolidayCount += 1;
                    $totalWorkingDays += 1;
                }
            } else {
                $status = 'not_marked';
                $totalWorkingDays += 1;
            }

            if ($status !== 'upcoming') {
                $isUserLate = $isAttendanceDataFound && $isAttendanceDataFound->is_late ? $isAttendanceDataFound->is_late : 0;

                $attendanceData[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => $status,
                    'is_holiday' => $isHoliday,
                    'holiday_name' => $holidayName,
                    'is_leave' => $isLeave,
                    'leave_name' => $leaveName,
                    'is_late'   => $isUserLate,
                    'late_time' => $lateTime,
                    'clock_in'  => $isAttendanceDataFound?->clock_in_date_time ?? '',
                    'clock_out' => $isAttendanceDataFound?->clock_out_date_time ?? '',
                    'clock_in_ip' => $isAttendanceDataFound?->clock_in_ip_address ?? '',
                    'clock_out_ip' => $isAttendanceDataFound?->clock_out_ip_address ?? '',
                    'leave_reason' => $leaveReason,
                    'worked_time' => $totalWorkDurationInMinutes
                ];

                $totalLateDays += $isUserLate;
                $clockedInDuration += $totalWorkDurationInMinutes;
            }

            $totalDays += 1;
            $endDate->subDay();
        }

        return [
            'data' => $attendanceData,
            'total_days' => $totalDays,
            'working_days' => $totalWorkingDays,
            'present_days' => $totalPresentDays,
            'paid_leaves' => $paidLeaveCount,
            'half_days' => $totalHalfDays,
            'office_time' => $officeHoursInMinutes,
            'total_office_time' => $totalWorkingDays * $officeHoursInMinutes,
            'clock_in_duration' => $clockedInDuration,
            'total_late_days' => $totalLateDays,
            'total_paid_leaves' => $totalPaidLeave,
            'total_unpaid_leaves' => $totalUnPaidLeave,
            'holiday_count' => $totalHolidayCount,
        ];
    }

    public static function attendanceDetailByMonth()
    {
        $request = request();

        $month = (int) $request->month;
        $year = (int) $request->year;
        $loggedUser = user();
        $company = company();

        if ($request->has('user_id') && $loggedUser->ability('admin', 'attendances_view')) {
            $userId = Common::getIdFromHash($request->user_id);
        } else {
            $userId = $loggedUser->id;
        }

        $resultData = self::getMonthYearAttendanceDetails($userId, $month, $year);

        return $resultData;
    }

    public static function applyVisibility($query, $joinTableName = 'users', $joinTableFieldName = 'user_id')
    {
        $user = user();

        // If user is not admin then
        // Users lists will be based on his visibility
        // don't show any user if deperatment, location or report_to is null assigned to user
        if ($user->role->name != 'admin') {
            if (in_array($user->visibility, ['department', 'location', 'manager']) && $joinTableName != 'users') {
                $query->join('users', 'users.id', '=', $joinTableName . '.' . $joinTableFieldName);
            }

            if ($user->visibility == 'department') {
                $query->where(function ($newQuery) use ($user, $query) {
                    $newQuery->where('users.department_id', $user->department_id)
                        ->whereNotNull('users.department_id');
                });
            } else if ($user->visibility == 'location') {
                $query->where(function ($newQuery) use ($user, $query) {
                    $newQuery->where('users.location_id', $user->location_id)
                        ->whereNotNull('users.location_id');
                });
            } else if ($user->visibility == 'manager') {
                $query->where(function ($newQuery) use ($user, $query) {
                    $newQuery->where('users.report_to', $user->id)
                        ->whereNotNull('users.report_to');
                });
            }
        }

        return $query;
    }


    public static function attendanceDetail($userId = null)
    {
        $request = request();
        $company = company();

        $month = (int) $request->month;
        $year = (int) $request->year;
        $loggedUser = user();

        $today = Carbon::now($company->timezone);
        $startDate = Carbon::createFromDate($year, $month, 1, $company->timezone)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();

        $dateRanges = [];
        $columns = [];
        $attendanceData = [];

        $offset = $request->offset;
        $limit = $request->limit;
        $totalRecords = 1;

        $allCompanyUsers = StaffMember::with([
            'location:id,name',
            'designation:id,name'
        ])->select('users.id', 'users.name', 'users.profile_image', 'users.location_id', 'users.designation_id');

        if ($loggedUser->ability('admin', 'attendances_view')) {
            $allCompanyUsers = self::applyVisibility($allCompanyUsers);

            if ($request->has('user_id') || $userId != null) {
                $userId = $request->has('user_id') ? Common::getIdFromHash($request->user_id) : $userId;
                $allCompanyUsers = $allCompanyUsers->where('id', $userId);
                $totalRecords = 1;
            } else {
                $totalRecords = StaffMember::select('users.id');
                $totalRecords = self::applyVisibility($totalRecords)->count();
            }
        } else {
            $userId = $userId != null ? $userId : $loggedUser->id;
            $allCompanyUsers = $allCompanyUsers->where('id', $userId);
            $totalRecords = 1;
        }

        $allCompanyUsers = $allCompanyUsers->orderBy('id', 'desc')->skip($offset)->take($limit)->get();

        $allAttendances = Attendance::with(['leave:id,leave_type_id,reason', 'leave.leaveType:id,name', 'holiday']);

        if ($userId != null) {
            $allAttendances = $allAttendances->where('attendances.user_id', $userId);
        }

        $allAttendances = $allAttendances->whereBetween('attendances.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->get();
        $allHolidays = Holiday::whereBetween('holidays.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->get();

        while ($startDate->lte($endDate)) {
            $dateRanges[] = $startDate->copy();
            $columns[] = $startDate->format('d');
            $startDate->addDay();
        }

        foreach ($allCompanyUsers as $allCompanyUser) {
            $userAttendanceData = [
                'name' => $allCompanyUser->name,
                'user' => $allCompanyUser,
            ];

            $totalWorkingDays = 0;
            $totalPresentDays = 0;

            foreach ($dateRanges as $dateRange) {
                $isAttendanceDataFound = $allAttendances->filter(function ($allAttendance) use ($dateRange, $allCompanyUser) {
                    return $allAttendance->date->format('Y-m-d') == $dateRange->format('Y-m-d') && $allCompanyUser->id == $allAttendance->user_id;
                })->first();

                $isHolidayDataFound = $allHolidays->filter(function ($allHoliday) use ($dateRange) {
                    return $allHoliday->date->format('Y-m-d') == $dateRange->format('Y-m-d');
                })->first();

                $isHoliday = $isHolidayDataFound ? true : false;
                $holidayName = $isHolidayDataFound ? $isHolidayDataFound->name : '';

                $isLeave = false;
                $leaveName = '';
                $leaveReason = '';
                $status = 'absent'; // default status

                if ($dateRange->gt($today)) {
                    $status = 'upcoming';
                } elseif ($isAttendanceDataFound) {
                    if ($isAttendanceDataFound->is_leave && $isAttendanceDataFound->is_half_day) {
                        $status = 'half_day';
                        $isLeave = true;
                        $leaveName = optional($isAttendanceDataFound->leave?->leaveType)->name;
                        $leaveReason = optional($isAttendanceDataFound->leave)->reason;
                        $totalPresentDays += 0.5;
                    } elseif ($isAttendanceDataFound->is_leave) {
                        $status = 'absent'; // leave, but considered absent
                        $isLeave = true;
                        $leaveName = optional($isAttendanceDataFound->leave?->leaveType)->name;
                        $leaveReason = optional($isAttendanceDataFound->leave)->reason;
                    } else {
                        $status = 'present';
                        $totalPresentDays += 1;
                    }

                    $totalWorkingDays += 1;
                } elseif ($isHolidayDataFound) {
                    $status = 'holiday'; // no attendance, so status = holiday
                } else {
                    $status = 'absent';
                    $totalWorkingDays += 1;
                }

                $userAttendanceData[$dateRange->format('j')] = [
                    'status' => $status,
                    'is_holiday' => $isHoliday,
                    'holiday_name' => $holidayName,
                    'is_leave' => $isLeave,
                    'leave_name' => $leaveName,
                    'clock_in'  => $isAttendanceDataFound?->clock_in_date_time ?? '',
                    'clock_out'  => $isAttendanceDataFound?->clock_out_date_time ?? '',
                    'leave_reason'  => $leaveReason,
                ];
            }

            $userAttendanceData['working_days'] = $totalWorkingDays;
            $userAttendanceData['present_days'] = $totalPresentDays;
            $attendanceData[] = $userAttendanceData;
        }

        return [
            'columns' => $columns,
            'dateRange' => $dateRanges,
            'data' => $attendanceData,
            'totalRecords' => $totalRecords
        ];
    }


    public static function markWeekend($markDayName, $startDate, $endDate, $ocassionName, $isHalfDay, $halfHoliday)
    {
        $periods = CarbonPeriod::create($startDate, $endDate);
        $dates = [];

        // Iterate over the period
        foreach ($periods as $period) {
            $date = $period->format('Y-m-d');
            $dayName = $period->format('l');
            $dates[] = $dayName;

            if (in_array($dayName, $markDayName)) {

                // Check if holiday exists
                $newHoliday = Holiday::whereDate('date', $date)->first();

                if (!$newHoliday) {
                    $newHoliday = new Holiday();
                }
                $newHoliday->date = $date;
                $newHoliday->name = $ocassionName;
                $newHoliday->year = $period->format('Y');
                $newHoliday->month = $period->format('m');
                $newHoliday->is_weekend = 1;
                $newHoliday->is_half_day = $isHalfDay;
                $newHoliday->half_holiday = $halfHoliday;
                $newHoliday->save();
            }
        }
    }

    public static function updateLeaveStatus($id)
    {
        $leave = Leave::find($id);
        $leaveDates = CarbonPeriod::create($leave->start_date, $leave->end_date);
        $data = [];

        foreach ($leaveDates as $leaveDate) {
            $date = $leaveDate->format('Y-m-d');

            // Get holiday and leave-related data
            $leaveInfo = self::isPaidLeaveOrNot($date, $leave->user_id, $leave->leave_type_id);

            $data[] = [
                'date' => $leaveDate,
                'totalLeaveTakenThisYear' => $leaveInfo['totalLeaveTakenThisYear'],
                'paidLeaveTakenThisMonth' => $leaveInfo['paidLeaveTakenThisMonth'],
                'total_leaves' => $leaveInfo['totalLeaves'],
                'maxLeavePerMonth' => $leaveInfo['maxLeavePerMonth'],
                'isPaid' => $leaveInfo['isPaid'],
            ];

            // Check if attendance already exists
            $existing = Attendance::where('user_id', $leave->user_id)
                ->whereDate('date', $date)
                ->first();

            if (!$existing) {
                $attendance = new Attendance();
                $attendance->is_leave = 1;
                $attendance->date = $date;
                $attendance->user_id = $leave->user_id;
                $attendance->leave_id = $leave->id;
                $attendance->leave_type_id = $leave->leave_type_id;
                $attendance->is_half_day = $leave->is_half_day;
                $attendance->is_paid = $leaveInfo['isPaid'];
                $attendance->status = $leave->is_half_day ? 'half_day' : 'on_leave';
                $attendance->reason = $leave->reason;
                $attendance->save();
            }
        }

        return $data;
    }


    public static function generateDatesFromToday($dayCount)
    {
        $dates = [];
        $today = Carbon::today();

        // Start from the oldest date and move towards today
        for ($i = $dayCount; $i >= 0; $i--) {
            $dates[] = $today->copy()->subDays($i)->toDateString();
        }

        return $dates;
    }
}
