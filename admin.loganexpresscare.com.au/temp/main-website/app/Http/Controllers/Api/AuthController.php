<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\ProfileRequest;
use App\Http\Requests\Api\Auth\RefreshTokenRequest;
use App\Http\Requests\Api\Auth\UploadFileRequest;
use App\Models\Appreciation;
use App\Models\Asset;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Complaint;
use App\Models\EmployeeWorkStatus;
use App\Models\Holiday;
use App\Models\Warning;
use App\Models\Expense;
use App\Models\FeedbackUser;
use App\Models\IncrementPromotion;
use App\Models\Lang;
use App\Models\Settings;
use App\Models\StaffMember;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Rats\Zkteco\Lib\ZKTeco;

class AuthController extends ApiBaseController
{
    public function companySetting()
    {
        $settings = Company::first();

        return ApiResponse::make('Success', [
            'global_setting' => $settings,
        ]);
    }

    public function emailSettingVerified()
    {
        $emailSettingVerified = Settings::where('setting_type', 'email')
            ->where('status', 1)
            ->where('verified', 1)
            ->count();

        return $emailSettingVerified > 0 ? 1 : 0;
    }

    public function app()
    {
        $company = company(true);
        $addMenuSetting = $company ? Settings::where('setting_type', 'shortcut_menus')->first() : null;

        return ApiResponse::make('Success', [
            'app' => $company,
            'shortcut_menus' => $addMenuSetting,
            'email_setting_verified' => $this->emailSettingVerified(),
            'email_variables' => Notify::mailVariables(),
        ]);
    }

    public function checkSubscriptionModuleVisibility()
    {
        $request = request();
        $itemType = $request->item_type;

        $visible = Common::checkSubscriptionModuleVisibility($itemType);

        return ApiResponse::make('Success', [
            'visible' => $visible,
        ]);
    }

    public function visibleSubscriptionModules()
    {
        $visibleSubscriptionModules = Common::allVisibleSubscriptionModules();

        return ApiResponse::make('Success', $visibleSubscriptionModules);
    }

    public function allEnabledLangs()
    {
        $allLangs = Lang::select('id', 'name', 'key', 'image')->where('enabled', 1)->get();

        return ApiResponse::make('Success', [
            'langs' => $allLangs
        ]);
    }


    public function login(LoginRequest $request)
    {
        // Removing all sessions before login
        session()->flush();

        $phone = "";
        $email = "";

        $credentials = [
            'password' =>  $request->password
        ];

        if (is_numeric($request->get('email'))) {
            $credentials['phone'] = $request->email;
            $phone = $request->email;
        } else {
            $credentials['email'] = $request->email;
            $email = $request->email;
        }

        // For checking user
        $user = StaffMember::select('*');
        if ($email != '') {
            $user = $user->where('email', $email);
        } else if ($phone != '') {
            $user = $user->where('phone', $phone);
        }
        $user = $user->first();

        // Adding user type according to email/phone
        if ($user) {
            $credentials['user_type'] = 'staff_members';
            $credentials['is_superadmin'] = 0;
            $userCompany = Company::where('id', $user->company_id)->first();
        }

        if (!$token = auth('api')->attempt($credentials)) {
            throw new ApiException('These credentials do not match our records.');
        } else if ($userCompany->status === 'pending') {
            throw new ApiException('Your company not verified.');
        } else if ($userCompany->status === 'inactive') {
            throw new ApiException('Company account deactivated.');
        } else if (auth('api')->user()->status === 'waiting') {
            throw new ApiException('User not verified.');
        } else if (auth('api')->user()->status === 'disabled') {
            throw new ApiException('Account deactivated.');
        }

        $company = company();
        $response = $this->respondWithToken($token);
        $addMenuSetting = Settings::where('setting_type', 'shortcut_menus')->first();
        $response['app'] = $company;
        $response['shortcut_menus'] = $addMenuSetting;
        $response['email_setting_verified'] = $this->emailSettingVerified();
        $response['email_variables'] = Notify::mailVariables();
        $response['visible_subscription_modules'] = Common::allVisibleSubscriptionModules();

        return ApiResponse::make('Loggged in successfull', $response);
    }

    protected function respondWithToken($token)
    {
        $user = user();

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Carbon::now()->addDays(180),
            'user' => $user
        ];
    }

    public function logout()
    {
        $request = request();

        if (auth('api')->user() && $request->bearerToken() != '') {
            auth('api')->logout();
        }

        session()->flush();

        return ApiResponse::make(__('Session closed successfully'));
    }

    public function user()
    {
        $user = auth('api')->user();
        $user = $user->load('role', 'role.perms');

        session(['user' => $user]);

        return ApiResponse::make('Data successfull', [
            'user' => $user
        ]);
    }

    public function refreshToken(RefreshTokenRequest $request)
    {
        $newToken = auth('api')->refresh();

        $response = $this->respondWithToken($newToken);

        return ApiResponse::make('Token fetched successfully', $response);
    }

    public function uploadFile(UploadFileRequest $request)
    {
        $result = Common::uploadFile($request);

        return ApiResponse::make('File Uploaded', $result);
    }


    public function profile(ProfileRequest $request)
    {
        $user = auth('api')->user();

        // In Demo Mode
        if (env('APP_ENV') == 'production') {
            $request = request();

            if ($request->email == 'admin@example.com' && $request->has('password') && $request->password != '') {
                throw new ApiException('Not Allowed In Demo Mode');
            }
        }

        $user->name = $request->name;
        if ($request->has('profile_image')) {
            $user->profile_image = $request->profile_image;
        }
        if ($request->password != '') {
            $user->password = $request->password;
        }
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return ApiResponse::make('Profile updated successfull', [
            'user' => $user->load('role', 'role.perms')
        ]);
    }

    public function langTrans()
    {
        $langs = Lang::with('translations')->get();

        return ApiResponse::make('Langs fetched', [
            'data' => $langs
        ]);
    }


    public function getTodaysPresent()
    {
        $request = request();
        $company = company();
        $currentDateTimeObject = Carbon::now($company->timezone);
        $currentDate = $currentDateTimeObject->copy()->format('Y-m-d');

        $startDate = $currentDate;
        $endDate = $currentDate;

        if ($request->has('dates') && is_array($request->dates) && count($request->dates) === 2) {
            $startDate = Carbon::parse($request->dates[0])->startOfDay()->format('Y-m-d');
            $endDate = Carbon::parse($request->dates[1])->endOfDay()->format('Y-m-d');
        }

        $colors = ["#20C997", "#FF4136", "#ffa040"];
        $status = ["Present", "Absent", "Not Marked"];

        $employeeUnderYou = StaffMember::select('id', 'name')
            ->where('status', 'active');

        $employeeUnderYou = $this->applyVisibility($employeeUnderYou, 'users')->get();
        $employeeUnderYouCount = $employeeUnderYou->count();


        $todayHoliday = Holiday::whereBetween('date', [$startDate, $endDate])->exists();
        $isHoliday = $todayHoliday ? true : false;

        $allAttendances = Attendance::whereBetween('date', [$startDate, $endDate])->get();

        $present = 0;
        $absent = 0;
        $notMarked = 0;

        foreach ($employeeUnderYou as $employee) {
            $isAttendanceDataFound = $allAttendances->firstWhere('user_id', $employee->id);
            if ($isAttendanceDataFound) {
                if ($isAttendanceDataFound->is_leave) {
                    $absent++;
                } else {
                    $present++;
                }
            } else {
                $notMarked++;
            }
        }

        $totalUsers = $present + $absent + $notMarked;

        $presentPercentage = $totalUsers > 0 ? round(($present / $totalUsers) * 100, 2) : 0;
        $absentPercentage = $totalUsers > 0 ? round(($absent / $totalUsers) * 100, 2) : 0;
        $notMarkedPercentage = $totalUsers > 0 ? round(($notMarked / $totalUsers) * 100, 2) : 0;

        $presentEmployes = [];
        $totalAttendence = [];
        $attendenceColor = [];
        $counter = 0;

        foreach ($status as $stats) {
            if ($stats == 'Absent') {
                $presentEmployes[] = $stats;
                $totalAttendence[] = $absent;
                $attendenceColor[] = $colors[$counter];
            } else if ($stats == 'Present') {
                $presentEmployes[] = $stats;
                $totalAttendence[] = $present;
                $attendenceColor[] = $colors[$counter];
            } else {
                $presentEmployes[] = $stats;
                $totalAttendence[] = $notMarked;
                $attendenceColor[] = $colors[$counter];
            }
            $counter++;
        }

        return [
            'labels' => $presentEmployes,
            'values' => $totalAttendence,
            'colors' => $attendenceColor,
            'employee_under_you' => $employeeUnderYouCount,
            'total_users' => $totalUsers,
            'percentages' => [
                'present' => $presentPercentage,
                'absent' => $absentPercentage,
                'not_marked' => $notMarkedPercentage,
            ],
            'employeeNumber' => [
                'present' => $present,
                'absent' => $absent,
                'not_marked' => $notMarked
            ]
        ];
    }

    public function getUpcomingBirthday()
    {
        $request = request();
        $userId = $this->getIdFromHash($request->userId);
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



    public function getUpcomingAnniversaries()
    {
        $request = request();
        $userId = $this->getIdFromHash($request->userId);
        $date = $request->date ? Carbon::parse($request->date) : null;
        $today = Carbon::today();
        $currentYear = $today->year;

        // Base query
        $query = StaffMember::where('status', 'active')->with('designation');

        // If userId is provided, filter by userId
        if ($userId) {
            $query->where('id', $userId);
        }

        // If date is provided, filter anniversaries by this date
        if ($date) {
            $query->whereRaw("DATE_FORMAT(joining_date, '%m-%d') = ?", [$date->format('m-d')]);
        }

        // Fetch and process anniversaries
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


    public function getStatsData()
    {

        $totalEmployees = StaffMember::where('user_type', 'staff_members')->count();
        $totalActiveEmployees = StaffMember::where('user_type', 'staff_members')
            ->where('status', 'active')->count();
        $totalInactiveEmployees = StaffMember::where('user_type', 'staff_members')
            ->where('status', 'inactive')->count();

        return [
            'totalEmployees' => $totalEmployees,
            'totalActiveEmployee' => $totalActiveEmployees,
            'totalInactiveEmployees' => $totalInactiveEmployees
        ];
    }

    public function getUsersCountByDepartment()
    {
        $users = StaffMember::whereNotNull('department_id')->with('department')->get();

        $departmentCounts = $users->groupBy(function ($user) {
            return $user->department ? $user->department->name : null;
        })->map(function ($group) {
            return $group->count();
        });

        $departments = $departmentCounts->keys()->values()->toArray();
        $employeeCounts = $departmentCounts->values()->toArray();

        return [
            'departments' => $departments,
            'employee_counts' => $employeeCounts,
        ];
    }

    public function getTodayAttendancesWithLateStatus()
    {
        $request = request();
        $attendances = collect();

        $today = Carbon::today()->format('Y-m-d');
        $startDate = $today;
        $endDate = $today;

        if ($request->has('clock_in_dates') && is_array($request->clock_in_dates) && count($request->clock_in_dates) === 2) {
            $startDate = Carbon::parse($request->clock_in_dates[0])->startOfDay()->format('Y-m-d');
            $endDate = Carbon::parse($request->clock_in_dates[1])->endOfDay()->format('Y-m-d');
        }

        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('clock_in_time', '!=', null)
            ->with(['user.designation'])
            ->orderBy('date', 'desc');
        $attendances = $this->applyVisibility($attendances, 'attendances')->get();
        return [
            'is_late_true' => [],
            'is_late_false' => $attendances,
        ];
    }

    public function categorizeHolidays()
    {
        $request = request();
        $year = $request->year;

        $holidays = Holiday::where('year', $year)->get();

        $weekends = $holidays->filter(function ($holiday) {
            return $holiday->is_weekend;
        })->sortBy(function ($holiday) {
            return $holiday->date;
        });

        $holidaysArray = $holidays->filter(function ($holiday) {
            return !$holiday->is_weekend;
        })->sortBy(function ($holiday) {
            return $holiday->date;
        });

        return [
            'weekends' => $weekends->values(),
            'holidays' => $holidaysArray->values(),
        ];
    }

    public function dashboard(Request $request)
    {
        $request = request();

        $types = [
            "employee_status" => fn() => ['getEmployeeStatus' => $this->getEmployeeStatus()],
            "weekendHoliday" => fn() => ['categorizeHolidays' => $this->categorizeHolidays()],
            "clock_in" => fn() => ['clockInOutTime' => $this->getTodayAttendancesWithLateStatus()],
            "attendance" => fn() => ['todaysAttendence' => $this->getTodaysPresent()],
            "employee_increment" => fn() => ['getIncrementPromotions' => $this->getIncrementPromotion()],
            "employee_appriciation" => fn() => ['appriciationByDate' => $this->appriciationByDate()],
            "birthday_data" => fn() => ['upcomingBirthday' => $this->getUpcomingBirthday()],
            "anniversary_data" => fn() => ['getUpcomingAnniversaries' => $this->getUpcomingAnniversaries()],
            "all" => fn() => [
                'todaysAttendence' => $this->getTodaysPresent(),
                'stateData' => $this->getStatsData(),
                'upcomingBirthday' => $this->getUpcomingBirthday(),
                'departmentByNameCount' => $this->getUsersCountByDepartment(),
                'clockInOutTime' => $this->getTodayAttendancesWithLateStatus(),
                'getUpcomingAnniversaries' => $this->getUpcomingAnniversaries(),
                'categorizeHolidays' => $this->categorizeHolidays(),
                'getEmployeeStatus' => $this->getEmployeeStatus(),
                'employeeWorkStatusCount' => $this->employeeWorkStatusCount(),
                'getIncrementPromotions' => $this->getIncrementPromotion(),
                'appriciationByDate' => $this->appriciationByDate()
            ]
        ];

        $data = $types[$request->type] ?? [];

        return ApiResponse::make('Data fetched', is_callable($data) ? $data() : []);
    }
    public function getEmployeeStatus()
    {
        $request = request();
        $id = $request->has('userId') ? $this->getIdFromHash($request->userId) : null;

        $startDate = Carbon::today()->format('Y-m-d');
        $endDate = Carbon::today()->format('Y-m-d');

        if ($request->has('status_date') && is_array($request->status_date) && count($request->status_date) === 2) {
            $startDate = Carbon::parse($request->status_date[0])->format('Y-m-d');
            $endDate = Carbon::parse($request->status_date[1])->format('Y-m-d');
        }

        $complaintsQuery = Complaint::whereBetween(DB::raw('DATE(date_time)'), [$startDate, $endDate]);
        $warningsQuery = Warning::whereBetween(DB::raw('DATE(warning_date)'), [$startDate, $endDate]);
        $expensesQuery = Expense::whereBetween(DB::raw('DATE(date_time)'), [$startDate, $endDate]);
        $appreciationsQuery = Appreciation::whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        $assetsQuery = Asset::whereBetween(DB::raw('DATE(purchase_date)'), [$startDate, $endDate]);
        $feedbackQuery = FeedbackUser::whereBetween(DB::raw('DATE(submit_date)'), [$startDate, $endDate]);

        if ($id) {
            $complaintsQuery->where('to_user_id', $id);
            $warningsQuery->where('user_id', $id);
            $expensesQuery->where('user_id', $id);
            $appreciationsQuery->where('user_id', $id);
            $assetsQuery->where('user_id', $id);
            $feedbackQuery->where('user_id', $id);
        }

        $complaintsCount = $this->applyVisibility($complaintsQuery, 'complaints', 'to_user_id')->count();
        $warningsCount = $this->applyVisibility($warningsQuery, 'warnings')->count();
        $expensesCount = $this->applyVisibility($expensesQuery, 'expenses')->count();
        $appreciationsCount = $this->applyVisibility($appreciationsQuery, 'appreciations')->count();
        $assetsCount = $this->applyVisibility($assetsQuery, 'assets')->count();
        $feedbackCount = $this->applyVisibility($feedbackQuery, 'feedback_users')->count();

        $topAppreciationUser = Appreciation::select('user_id', DB::raw('COUNT(*) as appreciation_count'))
            ->groupBy('user_id')
            ->orderByDesc('appreciation_count')
            ->first();
        $topUserAppreciationCount = $topAppreciationUser ? $topAppreciationUser->appreciation_count : 0;

        $user = $topAppreciationUser
            ? StaffMember::with('role:id,name')->find($topAppreciationUser->user_id)
            : null;

        return [
            'complaints' => $complaintsCount,
            'warnings' => $warningsCount,
            'expenses' => $expensesCount,
            'appreciations' => $appreciationsCount,
            'assetsCount' => $assetsCount,
            'feedbackCount' => $feedbackCount,
            'user' => $user,
            'top_user_appreciation_count' => $topUserAppreciationCount,
        ];
    }

    public function employeeWorkStatusCount()
    {
        $employeeCounts = StaffMember::select('employee_status_id', DB::raw('COUNT(*) as total'));

        $employeeCounts = $this->applyVisibility($employeeCounts, 'users')->groupBy('employee_status_id')
            ->pluck('total', 'employee_status_id');

        $workStatuses = EmployeeWorkStatus::whereIn('name', ['fulltime', 'contract', 'probation', 'work_from_home'])
            ->pluck('id', 'name')
            ->toArray();

        return [
            'fulltime' => isset($workStatuses['fulltime']) ? ($employeeCounts[$workStatuses['fulltime']] ?? 0) : 0,
            'contract' => isset($workStatuses['contract']) ? ($employeeCounts[$workStatuses['contract']] ?? 0) : 0,
            'probation' => isset($workStatuses['probation']) ? ($employeeCounts[$workStatuses['probation']] ?? 0) : 0,
            'work_from_home' => isset($workStatuses['work_from_home']) ? ($employeeCounts[$workStatuses['work_from_home']] ?? 0) : 0,
        ];
    }



    public function changeThemeMode(Request $request)
    {
        $mode = $request->has('theme_mode') ? $request->theme_mode : 'light';

        session(['theme_mode' => $mode]);

        if ($mode == 'dark') {
            $company = company();
            $company->left_sidebar_theme = 'dark';
            $company->save();

            $updatedCompany = company(true);
        }

        return ApiResponse::make('Success', [
            'status' => "success",
            'themeMode' => $mode,
            'themeModee' => session()->all(),
        ]);
    }

    public function getAllTimezones()
    {
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);

        return ApiResponse::make('Success', [
            'timezones' => $timezones,
            'date_formates' => [
                'd-m-Y' => 'DD-MM-YYYY',
                'm-d-Y' => 'MM-DD-YYYY',
                'Y-m-d' => 'YYYY-MM-DD',
                'd.m.Y' => 'DD.MM.YYYY',
                'm.d.Y' => 'MM.DD.YYYY',
                'Y.m.d' => 'YYYY.MM.DD',
                'd/m/Y' => 'DD/MM/YYYY',
                'm/d/Y' => 'MM/DD/YYYY',
                'Y/m/d' => 'YYYY/MM/DD',
                'd/M/Y' => 'DD/MMM/YYYY',
                'd.M.Y' => 'DD.MMM.YYYY',
                'd-M-Y' => 'DD-MMM-YYYY',
                'd M Y' => 'DD MMM YYYY',
                'd F, Y' => 'DD MMMM, YYYY',
                'D/M/Y' => 'ddd/MMM/YYYY',
                'D.M.Y' => 'ddd.MMM.YYYY',
                'D-M-Y' => 'ddd-MMM-YYYY',
                'D M Y' => 'ddd MMM YYYY',
                'd D M Y' => 'DD ddd MMM YYYY',
                'D d M Y' => 'ddd DD MMM YYYY',
                'dS M Y' => 'Do MMM YYYY',
            ],
            'time_formates' => [
                "hh:mm A" => '12 Hours hh:mm A',
                'hh:mm a' => '12 Hours hh:mm a',
                'hh:mm:ss A' => '12 Hours hh:mm:ss A',
                'hh:mm:ss a' => '12 Hours hh:mm:ss a',
                'HH:mm ' => '24 Hours HH:mm:ss',
                'HH:mm:ss' => '24 Hours hh:mm:ss',
            ]
        ]);
    }

    public function getIncrementPromotion()
    {
        $request = request();
        $filterMonthYear = $request->filterMonthYear;
        $userId = $request->userId ? $this->getIdFromHash($request->userId) : null;

        $query = IncrementPromotion::with(['user', 'currentDesignation', 'promotedDesignation'])
            ->orderBy('date', 'desc');

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        if (!empty($filterMonthYear) && is_numeric($filterMonthYear)) {
            $query->whereYear('date', $filterMonthYear);
        }

        return $query->get();
    }

    public function appriciationByDate()
    {
        $request = request();
        $filterMonthYear = $request->filterMonthYear; // Year as string (e.g., "2023" or "2025")
        $userId = $request->userId ? $this->getIdFromHash($request->userId) : null;

        $query = DB::table('appreciations')
            ->selectRaw('MONTH(date) as month_number, DATE_FORMAT(date, "%M") as month, COUNT(*) as count');

        // Apply user filter if provided
        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        // Apply year filter if provided, otherwise count for all years
        if (!empty($filterMonthYear) && is_numeric($filterMonthYear)) {
            $query->whereYear('date', $filterMonthYear);
        }

        $appriciationCounts = $query
            ->groupByRaw('MONTH(date), DATE_FORMAT(date, "%M")')
            ->orderBy('month_number')
            ->get();

        return $appriciationCounts;
    }

    public function downloadStaticFile($path)
    {
        // Point to public/uploads/
        $baseDir = realpath(public_path('uploads'));
        $filePath = realpath(public_path('uploads/' . $path));

        // Check if file exists and is inside the uploads folder
        if (
            !$filePath ||
            !file_exists($filePath) ||
            !str_starts_with($filePath, $baseDir)
        ) {
            return response()->json(['error' => 'File not found or forbidden'], 404);
        }

        return response()->file($filePath, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
        ]);
    }

    public function biometric($ip, $port = 4370)
    {
        // $zk = new ZKTeco('192.168.226.212', 4370);
        // $zk->connect();
        // $zk->disableDevice();
        // $version = $zk->getAttendance();
        // $zk->enableDevice();
        // dd($version);

        try {
            $zk = new ZKTeco($ip, $port);

            if (!$zk->connect()) {
                return response()->json(['message' => 'Unable to connect to device'], 500);
            }

            $zk->disableDevice();
            $logs = $zk->getAttendance();
            $zk->enableDevice();

            // $employeeId = count($logs) + 1;
            // $employeeNumber = 'EMP-' . $employeeId;
            // $newUser = [
            // 'id' => $employeeId,
            // 'employee_number' => $employeeNumber,
            // 'name' => 'Anuj Kumawat 1',
            // 'password' => '',
            // 'role' => 0, // 0 for user, 14 for admin
            // 'card' => 0,
            // ];
            // $zk->setUser($newUser['id'], $newUser['employee_number'], $newUser['name'], $newUser['password'], $newUser['role'], $newUser['card']);

            $zk->disconnect();

            return response()->json(['data' => $logs], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
