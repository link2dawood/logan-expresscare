<?php

namespace App\Http\Controllers\Api\Self;

use App\Classes\SelfCommonHrm;
use App\Http\Controllers\ApiBaseController;
use App\Models\Appreciation;
use App\Models\Attendance;
use App\Models\CompanyPolicy;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\Feedback;
use App\Models\FeedbackUser;
use App\Models\Holiday;
use App\Models\IncrementPromotion;
use App\Models\Leave;
use App\Models\News;
use App\Models\StaffMember;
use App\Models\Warning;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends ApiBaseController
{
    protected $model = Appreciation::class;


    public function summary()
    {
        $user = user();
        $company = company();
        $request = request();
        $data = [];

        $Date =  Carbon::now()->format("Y-m-d");

        $appreciations = Appreciation::where('user_id', $user->id)->orderBy("id", "desc")->with(['user', 'award'])
            ->count();

        $expenses = Expense::where('user_id', $user->id)->orderBy("id", "desc")->with(['user', 'award'])
            ->count();

        $warnings = Warning::where('user_id', $user->id)->orderBy("id", "desc")->with(['user', 'award'])
            ->count();

        $complaints = Complaint::where('to_user_id', $user->id)->orderBy("id", "desc")->with(['user', 'award'])
            ->count();

        // latest Birthday
        $birthdays = StaffMember::where('user_type', 'staff_members')
            ->where('dob', '>=', $Date)
            ->where('status', 'active')
            ->orderBy("dob", "asc")
            ->take(10)
            ->get();

        // latest 10 holidays
        $holidays = Holiday::where('date', '>=', $Date)->orderBy("id", "desc")
            ->take(10)
            ->get();

        // latest news
        $news = News::join('news_users', 'news_users.news_id', '=', 'news.id')
            ->where('news_users.user_id', $user->id)
            ->take(10)
            ->get();

        $surveies = Feedback::join('feedback_users', 'feedback_users.feedback_id', '=', 'feedbacks.id')
            ->where('feedback_users.user_id', $user->id)
            ->where('feedback_users.feedback_given', 0)
            ->where('feedbacks.last_date', '>=', $Date)->orderBy("feedbacks.id", "desc")->with(['feedbackUser.user'])
            ->take(10)
            ->get();

        $currentDateTimeObject = Carbon::now($company->timezone);
        $currentDate = $currentDateTimeObject->copy()->format('Y-m-d');
        $allAttendances = Attendance::whereDate('attendances.date', $currentDate)->where('user_id', $user->id)->with(['user', 'user.shift', 'leave', 'leaveType', 'holiday'])->get();

        if ($request->has('type') && $request->type == "weekendHoliday") {
            $data = ['categorizeHolidays' => $this->categorizeHolidays()];
        } else if ($request->has('type') && $request->type == "attendance_bar") {
            $data = ['attendanceSummaryByDate' => $this->attendanceSummaryByDate()];
        } else if ($request->has('type') && $request->type == "employee_increment") {
            $data = ['getIncrementPromotions' => $this->getIncrementPromotion()];
        } else if ($request->has('type') && $request->type == "employee_appriciation") {
            $data = ['appriciationByDate' => $this->appriciationByDate()];
        } else if ($request->has('type') && $request->type == "birthday_data") {
            $data = ['upcomingBirthday' => $this->upcomingBirthday()];
        } else if ($request->has('type') && $request->type == "anniversary_data") {
            $data = ['getUpcomingAnniversaries' => $this->anniversaries()];
        } else {
            $data = [
                'appreciations' => $appreciations,
                'birthdays' => $birthdays,
                'holidays' => $holidays,
                'news' => $news,
                'surveies' => $surveies,
                'user' => $user,
                'attendances' => $this->attendanceByDate(),
                'leaves_summary' =>  $this->leaveSummary(),
                'expenses' => $expenses,
                'warnings' => $warnings,
                'complaints' => $complaints,
                'attendanceSummary' => $this->attendanceSummary(),
                'upcomingBirthday' => $this->upcomingBirthday(),
                'getUpcomingAnniversaries' => $this->anniversaries(),
                'categorizeHolidays' => $this->categorizeHolidays(),
                'attendanceSummaryByDate' => $this->attendanceSummaryByDate(),
                'getLatestPolicy' => $this->getLatestPolicy(),
                'getFeedbackUsers' => $this->getFeedbackUsers(),
                'getIncrementPromotions' => $this->getIncrementPromotion(),
                'appriciationByDate' => $this->appriciationByDate()
            ];
        }
        return ApiResponse::make('Data fetched', $data);
    }

    public function appriciationByDate()
    {
        $request = request();
        $filterMonthYear = $request->filterMonthYear;
        $userId = $this->getIdFromHash($request->userd);

        $query = DB::table('appreciations')
            ->selectRaw('MONTH(date) as month_number, DATE_FORMAT(date, "%M") as month, COUNT(*) as count')
            ->where('user_id', $userId);

        if (!empty($filterMonthYear) && is_numeric($filterMonthYear)) {
            $query->whereYear('date', $filterMonthYear);
        }

        $appriciationCounts = $query
            ->groupByRaw('MONTH(date), DATE_FORMAT(date, "%M")')
            ->orderBy('month_number')
            ->get();

        return $appriciationCounts;
    }

    public function leaveSummary()
    {
        $user = user();
        $allLeaves = Leave::where('user_id', $user->id)
            ->with('leaveType')
            ->get();

        $leaveSummary = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'paid_leaves' => 0,
            'unpaid_leaves' => 0,
            'total_leaves' => 0,
        ];

        foreach ($allLeaves as $leave) {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            $days = $startDate->diffInDays($endDate) + 1; // Include the start date

            if ($leave->status === 'pending') {
                $leaveSummary['pending'] += $days;
            } elseif ($leave->status === 'approved') {
                $leaveSummary['approved'] += $days;
            } elseif ($leave->status === 'rejected') {
                $leaveSummary['rejected'] += $days;
            }

            if ($leave->is_paid) {
                $leaveSummary['paid_leaves'] += $days;
            } else {
                $leaveSummary['unpaid_leaves'] += $days;
            }

            $leaveSummary['total_leaves'] += $days;
        }

        return $leaveSummary;
    }

    public function attendanceSummary()
    {
        $user = user();
        $attendanceSummary = DB::table('attendances')
            ->where('user_id', $user->id)
            ->selectRaw('
            COUNT(*) as total_attendance,
            SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as total_present,
            SUM(CASE WHEN status = "on_leave" THEN 1 ELSE 0 END) as total_on_leave,
            SUM(CASE WHEN is_half_day = true THEN 1 ELSE 0 END) as total_half_days,
            SUM(CASE WHEN is_late = true THEN 1 ELSE 0 END) as total_late
        ')->first();

        return $attendanceSummary;
    }

    public function upcomingBirthday()
    {
        $request = request();
        $userId = $this->getIdFromHash($request->filterUserId);
        $date = $request->date ? Carbon::parse($request->date) : null;
        $today = Carbon::today();
        $currentYear = $today->year;

        $query = StaffMember::where('status', 'active')->with('designation');

        if ($date) {
            $query->whereRaw("DATE_FORMAT(dob, '%m-%d') = ?", [$date->format('m-d')]);
        }

        if ($userId) {
            $query->where('id', $userId);
        }

        $upcomingBirthdays = $query
            ->orderByRaw("DATE_FORMAT(dob, '%m-%d') ASC")
            ->get()
            ->map(function ($user) use ($currentYear) {
                $dob = Carbon::parse($user->dob);
                $formattedDate = Carbon::create($currentYear, $dob->month, $dob->day)->format('j F Y');
                return [
                    'name' => $user->name,
                    'designation' => $user->designation->name ?? null,
                    'formatted_date' => $formattedDate,
                    'image_url' => $user->profile_image_url
                ];
            });

        return $upcomingBirthdays;
    }

    public function anniversaries()
    {
        $request = request();
        $userId = $this->getIdFromHash($request->filterUserId);
        $date = $request->date ? Carbon::parse($request->date) : null;
        $today = Carbon::today();
        $currentYear = $today->year;

        $query = StaffMember::where('status', 'active')->with('designation');

        if ($userId) {
            $query->where('id', $userId);
        }

        if ($date) {
            $query->whereRaw("DATE_FORMAT(joining_date, '%m-%d') = ?", [$date->format('m-d')]);
        }

        $anniversaries = $query
            ->orderByRaw("DATE_FORMAT(joining_date, '%m-%d') ASC")
            ->get()
            ->map(function ($user) use ($currentYear, $today) {
                $joiningDate = Carbon::parse($user->joining_date);
                $yearsCompleted = $currentYear - $joiningDate->year;

                if ($yearsCompleted === 0) {
                    return null;
                }

                $day = $joiningDate->format('j');
                $month = $joiningDate->format('F');
                $anniversaryDate = Carbon::createFromFormat('m-d', $joiningDate->format('m-d'));

                return [
                    'name' => $user->name,
                    'designation' => $user->designation->name ?? null,
                    'anniversary_count' => $yearsCompleted . $this->getOrdinalSuffix($yearsCompleted),
                    'formatted_date' => $day . $this->getOrdinalSuffix($day) . ' ' . $month,
                    'sort_date' => $anniversaryDate,
                    'profile_image_url' => $user->profile_image_url
                ];
            })
            ->filter()
            ->filter(fn($anniversary) => $anniversary['sort_date']->greaterThanOrEqualTo($today))
            ->sortBy('sort_date')
            ->values();

        return $anniversaries;
    }
    private function getOrdinalSuffix($number)
    {
        if (!in_array(($number % 100), [11, 12, 13])) {
            switch ($number % 10) {
                case 1:
                    return 'st';
                case 2:
                    return 'nd';
                case 3:
                    return 'rd';
            }
        }
        return 'th';
    }

    public function categorizeHolidays()
    {
        $request = request();
        $year = $request->year;
        $currentDate = now();

        $holidays = Holiday::where('year', $year)->get();

        $weekends = $holidays->filter(function ($holiday) {
            return $holiday->is_weekend;
        })->sortBy('date');

        $holidaysArray = $holidays->filter(function ($holiday) {
            return !$holiday->is_weekend;
        })->sortBy('date');

        $nextHoliday = $holidays->filter(function ($holiday) use ($currentDate) {
            return $holiday->date > $currentDate;
        })->sortBy('date')->first();

        if ($nextHoliday) {
            $nextHoliday->formatted_date = \Carbon\Carbon::parse($nextHoliday->date)->format('d M Y');
        }

        return [
            'weekends' => $weekends->values(),
            'holidays' => $holidaysArray->values(),
            'next_holiday' => $nextHoliday,
        ];
    }


    public function attendanceSummaryByDate()
    {
        $result = SelfCommonHrm::attendanceDetailByDate();

        return $result;
    }

    public function attendanceByDate()
    {
        $result = SelfCommonHrm::attendanceDetailByDate($type = true);

        return $result;
    }

    public function getLatestPolicy()
    {
        return CompanyPolicy::orderByRaw('GREATEST(created_at, updated_at) DESC')->first();
    }

    public function getFeedbackUsers()
    {
        $user = user();
        $feedbackUsers = FeedbackUser::with(['user', 'feedback'])
            ->where('user_id', $user->id)
            ->where('feedback_given', '==', 0)
            ->orderBy('id', 'desc')
            ->get();
        return $feedbackUsers;
    }

    public function getIncrementPromotion()
    {
        $request = request();
        $filterMonthYear = $request->filterMonthYear;
        $userId = $this->getIdFromHash($request->userId);

        $query = IncrementPromotion::with(['user', 'currentDesignation', 'promotedDesignation'])
            ->where('user_id', $userId);

        if (!empty($filterMonthYear) && is_numeric($filterMonthYear)) {
            $query->whereYear('date', $filterMonthYear);
        }

        return $query->orderBy('date', 'desc')->get();
    }
}
