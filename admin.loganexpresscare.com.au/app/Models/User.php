<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;
use App\Traits\EntrustUserTrait;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash as FacadesHash;
use Illuminate\Database\Eloquent\Builder;

class User extends BaseModel implements AuthenticatableContract, JWTSubject
{
    use Notifiable, EntrustUserTrait, Authenticatable, HasFactory;

    protected $table = 'users';

    protected $default = ["xid", "name", "employee_number", "joining_date", "probation_end_date", "probation_start_date", "profile_image", "notice_end_date", "notice_start_date", "address", "end_date", "dob", 'profile_image_url', 'location_id', 'designation_id', 'department_id', 'shift_id', "is_manager"];

    protected $guarded = ['id', 'company_id', 'created_at', 'updated_at'];

    protected $dates = ['last_active_on'];

    protected $hidden = ['id', 'role_id', 'employee_status_id', 'password', 'remember_token', 'department_id', 'designation_id', 'shift_id', 'location_id', 'salary_group_id'];

    protected $appends = ['xid', 'x_company_id', 'x_employee_status_id', 'x_role_id', 'x_salary_group_id', 'x_report_to', 'profile_image_url', 'x_department_id', 'x_designation_id', 'x_shift_id', 'x_location_id', 'duration'];

    protected $filterable = ['name', 'user_type', 'email', 'status', 'phone', 'shift_id'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXRoleIdAttribute' => 'role_id',
        'getXDepartmentIdAttribute' => 'department_id',
        'getXDesignationIdAttribute' => 'designation_id',
        'getXShiftIdAttribute' => 'shift_id',
        'getXLocationIdAttribute' => 'location_id',
        'getXReportToAttribute' => 'report_to',
        'getXSalaryGroupIdAttribute' => 'salary_group_id',
        'getXEmployeeStatusIdAttribute' => 'employee_status_id'
    ];

    protected $casts = [
        'company_id' => Hash::class . ':hash',
        'role_id' => Hash::class . ':hash',
        'employee_status_id' => Hash::class . ':hash',
        'login_enabled' => 'integer',
        'is_superadmin' => 'integer',
        'department_id' => Hash::class . ':hash',
        'designation_id' => Hash::class . ':hash',
        'location_id' => Hash::class . ':hash',
        'shift_id' => Hash::class . ':hash',
        'is_married' => 'integer',
        'is_manager' => 'integer',
        'report_to' => Hash::class . ':hash',
        'last_active_on' => 'datetime',
        'salary_group_id' => Hash::class . ':hash',
        'probation_start_date' => 'date',
        'probation_end_date' => 'date',
        'notice_start_date' => 'date',
        'notice_end_date' => 'date',
        'basic_salary' => 'double'
    ];

    protected $permissions = ['salary_settings', 'leaves_view', 'assets_view', 'leave_types_view'];

    protected static function boot()
    {
        parent::boot();

        // static::addGlobalScope('type', function (Builder $builder) {
        //     $builder->where('users.user_type', '=', 'staff_members');
        // });
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function employeeWorkStatus()
    {
        return $this->belongsTo(EmployeeWorkStatus::class, 'employee_status_id', 'id');
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = FacadesHash::make($value);
        }
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setUserTypeAttribute($value)
    {
        $this->attributes['user_type'] = 'staff_members';
    }

    public function getProfileImageUrlAttribute()
    {
        $userImagePath = Common::getFolderPath('userImagePath');

        return $this->profile_image == null ? asset('images/user.png') : Common::getFileUrl($userImagePath, $this->profile_image);
    }

    // public function getUserDocumentUrlAttribute()
    // {
    //     $userDocumentPath = Common::getFolderPath('userDocumentPath');

    //     return $this->values == null ? null : Common::getFileUrl($userDocumentPath, $this->values);
    // }

    public function getDurationAttribute()
    {
        $joiningDate = $this->joining_date;
        $endDate = $this->end_date;


        if ($joiningDate != null) {
            if ($endDate == null) {
                $endDates = Carbon::parse($endDate);

                // Calculate the difference in years, months, and days
                $years = $endDates->diffInYears($joiningDate);
                $months = $endDates->copy()->subYears($years)->diffInMonths($joiningDate);
                $days = $endDates->copy()->subYears($years)->subMonths($months)->diffInDays($joiningDate);
            } else {
                $now = Carbon::now();

                // Calculate the difference in years, months, and days
                $years = $now->diffInYears($joiningDate);
                $months = $now->copy()->subYears($years)->diffInMonths($joiningDate);
                $days = $now->copy()->subYears($years)->subMonths($months)->diffInDays($joiningDate);
            }

            $durationParts = [];

            if ($years > 0) {
                $durationParts[] = "{$years} y";
            }

            if ($months > 0) {
                $durationParts[] = "{$months} m";
            }

            if ($days > 0) {
                // Always show days if there are no other parts
                $durationParts[] = "{$days} d";
            }

            return implode(' ', $durationParts);
        }

        return "";
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function reporter()
    {
        return $this->hasOne(User::class, 'id', 'report_to');
    }

    public function appreciation()
    {
        return $this->hasMany(Appreciation::class, 'user_id',  'id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class,  'user_id',  'id');
    }

    public function leaveType()
    {
        return $this->hasMany(LeaveType::class, 'created_by', 'id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class,  'user_id',  'id');
    }

    public function assets_type()
    {
        return $this->belongsTo(AssetType::class, 'user_id',  'id');
    }

    public function salaryGroup()
    {
        return $this->hasOne(SalaryGroup::class, 'id', 'salary_group_id');
    }

    public function salaryGroupUsers()
    {
        return $this->hasMany(SalaryGroupUser::class, 'user_id');
    }

    public function basicSalaryDetails()
    {
        return $this->hasMany(BasicSalaryDetails::class, 'user_id', 'id');
    }

    public function userDocument()
    {
        return $this->hasMany(UserDocument::class, 'user_id');
    }
}
