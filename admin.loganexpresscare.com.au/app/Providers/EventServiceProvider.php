<?php

namespace App\Providers;


use App\Models\Company;
use App\Models\Currency;
use App\Models\Role;
use App\Models\Settings;
use App\Models\StaffMember;
use App\Models\Shift;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Holiday;
use App\Models\Appreciation;
use App\Models\Attendance;
use App\Models\Award;
use App\Models\IncrementPromotion;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Payroll;
use App\Models\PrePayment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Location;
use App\Models\Asset;
use App\Models\News;
use App\Models\AssetType;
use App\Models\Complaint;
use App\Models\Form;
use App\Models\Feedback;
use App\Models\CompanyPolicy;
use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\BasicSalaryDetails;
use App\Models\Payee;
use App\Models\Payer;
use App\Models\DepositCategory;
use App\Models\Deposit;
use App\Models\EmployeeWorkStatus;
use App\Models\Generate;
use App\Models\LetterHeadTemplate;
use App\Observers\AccountEntryObserver;
use App\Models\Warning;
use App\Models\Offboarding;
use App\Models\Indicator;
use App\Models\PayrollComponent;
use App\Models\SalaryComponent;
use App\Models\SalaryGroup;
use App\Models\SalaryGroupComponent;
use App\Models\SalaryGroupUser;
use App\Models\PdfFonts;
use App\Models\FieldTypes;
use App\Models\EmployeeFields;
use App\Models\EmployeeSpecificLeaveCount;
use App\Models\Self\SelfComplaint;
use App\Models\UserDocument;
use App\Models\WorkDuration;
use App\Observers\WorkDurationObserver;
use App\Observers\CurrencyObserver;
use App\Observers\PdfFontsObserver;
use App\Observers\FeedbackObserver;
use App\Observers\FormObserver;
use App\Observers\AssetTypeObserver;
use App\Observers\ComplaintObserver;
use App\Observers\LocationObserver;
use App\Observers\NewsObserver;
use App\Observers\AssetObserver;
use App\Observers\ExpenseCategoryObserver;
use App\Observers\ExpenseObserver;
use App\Observers\PayrollObserver;
use App\Observers\PrePaymentObserver;
use App\Observers\LeaveObserver;
use App\Observers\LeaveTypeObserver;
use App\Observers\IncrementPromotionObserver;
use App\Observers\AppreciationObserver;
use App\Observers\AttendanceObserver;
use App\Observers\AwardObserver;
use App\Observers\RoleObserver;
use App\Observers\SettingObserver;
use App\Observers\StaffMemberObserver;
use App\SuperAdmin\Models\SuperAdmin;
use App\SuperAdmin\Observers\SuperAdminObserver;
use App\SuperAdmin\Observers\CompanyObserver;
use App\Observers\DepartmentObserver;
use App\Observers\DesignationObserver;
use App\Observers\ShiftObserver;
use App\Observers\HolidayObserver;
use App\Observers\CompanyPolicyObserver;
use App\Observers\AccountObserver;
use App\Observers\BasicSalaryDetailsObserver;
use App\Observers\PayeeObserver;
use App\Observers\PayerObserver;
use App\Observers\DepositCategoryObserver;
use App\Observers\DepositObserver;
use App\Observers\EmployeeFieldsObserver;
use App\Observers\EmployeeSpecificLeaveCountObserver;
use App\Observers\EmployeeWorkStatusObserver;
use App\Observers\FieldTypesObserver;
use App\Observers\GenerateObserver;
use App\Observers\LetterHeadTemplateObserver;
use App\Observers\WarningObserver;
use App\Observers\ResignationObserver;
use App\Observers\IndicatorObserver;
use App\Observers\PayrollComponentObserver;
use App\Observers\SalaryComponentObserver;
use App\Observers\SalaryGroupComponentObserver;
use App\Observers\SalaryGroupObserver;
use App\Observers\SalaryGroupUserObserver;
use App\Observers\Self\SelfComplaintObserver;
use App\Observers\UserDocumentObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // Don't run observer when
        // we run command using
        if (!app()->runningInConsole()) {
            $appType = app_type();

            StaffMember::observe(StaffMemberObserver::class);
            Settings::observe(SettingObserver::class);
            Currency::observe(CurrencyObserver::class);
            Asset::observe(AssetObserver::class);
            Role::observe(RoleObserver::class);
            Department::observe(DepartmentObserver::class);
            Designation::observe(DesignationObserver::class);
            Holiday::observe(HolidayObserver::class);
            LeaveType::observe(LeaveTypeObserver::class);
            Location::observe(LocationObserver::class);
            AssetType::observe(AssetTypeObserver::class);
            Complaint::observe(ComplaintObserver::class);
            Form::observe(FormObserver::class);
            Feedback::observe(FeedbackObserver::class);
            News::observe(NewsObserver::class);
            Leave::observe(LeaveObserver::class);
            Shift::observe(ShiftObserver::class);
            PrePayment::observe(PrePaymentObserver::class);
            Attendance::observe(AttendanceObserver::class);
            Award::observe(AwardObserver::class);
            Appreciation::observe(AppreciationObserver::class);
            IncrementPromotion::observe(IncrementPromotionObserver::class);
            Payroll::observe(PayrollObserver::class);
            Expense::observe(ExpenseObserver::class);
            ExpenseCategory::observe(ExpenseCategoryObserver::class);
            CompanyPolicy::observe(CompanyPolicyObserver::class);
            Account::observe(AccountObserver::class);
            Payee::observe(PayeeObserver::class);
            Payer::observe(PayerObserver::class);
            DepositCategory::observe(DepositCategoryObserver::class);
            Deposit::observe(DepositObserver::class);
            AccountEntry::observe(AccountEntryObserver::class);
            Warning::observe(WarningObserver::class);
            Offboarding::observe(ResignationObserver::class);
            LetterHeadTemplate::observe(LetterHeadTemplateObserver::class);
            Generate::observe(GenerateObserver::class);
            Indicator::observe(IndicatorObserver::class);
            SalaryGroup::observe(SalaryGroupObserver::class);
            SalaryComponent::observe(SalaryComponentObserver::class);
            SalaryGroupComponent::observe(SalaryGroupComponentObserver::class);
            SalaryGroupUser::observe(SalaryGroupUserObserver::class);
            BasicSalaryDetails::observe(BasicSalaryDetailsObserver::class);
            PayrollComponent::observe(PayrollComponentObserver::class);
            PdfFonts::observe(PdfFontsObserver::class);
            EmployeeWorkStatus::observe(EmployeeWorkStatusObserver::class);
            WorkDuration::observe(WorkDurationObserver::class);
            FieldTypes::observe(FieldTypesObserver::class);
            EmployeeFields::observe(EmployeeFieldsObserver::class);
            UserDocument::observe(UserDocumentObserver::class);
            EmployeeSpecificLeaveCount::observe(EmployeeSpecificLeaveCountObserver::class);

            // Self Observer
            SelfComplaint::observe(SelfComplaintObserver::class);

            if ($appType == 'saas') {
                Company::observe(CompanyObserver::class);
                SuperAdmin::observe(SuperAdminObserver::class);
            }
        }
    }
}
