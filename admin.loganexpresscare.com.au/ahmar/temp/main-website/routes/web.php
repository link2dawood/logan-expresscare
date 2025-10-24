<?php

use Examyou\RestAPI\Facades\ApiRoute;
use Illuminate\Support\Facades\Route;

Route::get('static-file/{path}', ['as' => 'api.extra.static-file', 'uses' => 'App\Http\Controllers\Api\AuthController@downloadStaticFile'])->where('path', '.*');

// Admin Routes
ApiRoute::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    ApiRoute::get('all-langs', ['as' => 'api.extra.all-langs', 'uses' => 'AuthController@allEnabledLangs']);
    ApiRoute::get('lang-trans', ['as' => 'api.extra.lang-trans', 'uses' => 'AuthController@langTrans']);
    ApiRoute::post('change-theme-mode', ['as' => 'api.extra.change-theme-mode', 'uses' => 'AuthController@changeThemeMode']);
    ApiRoute::post('attendance-csv', ['as' => 'api.extra.attendance-csv', 'uses' => 'PdfController@downloadAttendanceCsv']);
    ApiRoute::post('attendance-xlsx', ['as' => 'api.extra.attendance-xlsx', 'uses' => 'PdfController@downloadAttendanceExcel']);
    ApiRoute::post('attendance-pdf', ['as' => 'api.extra.attendance-pdf', 'uses' => 'PdfController@attendancePdf']);
    ApiRoute::post('attendance-static-csv', ['as' => 'api.extra.attendance-static-csv', 'uses' => 'PdfController@downloadAttendanceStaticCsv']);
    ApiRoute::post('langs-static-csv', ['as' => 'api.extra.langs-static-csv', 'uses' => 'PdfController@downloadLangsStaticCsv']);
    ApiRoute::post('expense-csv', ['as' => 'api.extra.expense-csv', 'uses' => 'PdfController@downloadExpenseCsv']);
    ApiRoute::post('expense-xlsx', ['as' => 'api.extra.expense-xlsx', 'uses' => 'PdfController@downloadExpenseExcel']);
    ApiRoute::post('expense-pdf', ['as' => 'api.extra.expense-pdf', 'uses' => 'PdfController@expensePdf']);
    ApiRoute::post('langs-csv/{id}', ['as' => 'api.extra.lang-csv', 'uses' => 'PdfController@downloadLangCsv']);

    // Check visibility of module according to subscription plan
    ApiRoute::post('check-subscription-module-visibility', ['as' => 'api.extra.check-subscription-module-visibility', 'uses' => 'AuthController@checkSubscriptionModuleVisibility']);
    ApiRoute::post('visible-subscription-modules', ['as' => 'api.extra.visible-subscription-modules', 'uses' => 'AuthController@visibleSubscriptionModules']);

    ApiRoute::group(['middleware' => ['api.auth.check']], function () {

        // Moved in api.auth.check middleware
        // because it as giving the company() issue
        // company() return null
        ApiRoute::post('company-policy-pdf/{xid?}', ['as' => 'api.extra.company-policy-pdf', 'uses' => 'PdfController@companyPolicyPdf']);
        ApiRoute::post('holiday-pdf', ['as' => 'api.extra.holiday-pdf', 'uses' => 'PdfController@holidayPdf']);
        ApiRoute::post('generate-pdf/{xid?}', ['as' => 'api.extra.generate-pdf', 'uses' => 'PdfController@generatePdf']);
        ApiRoute::post('account-pdf', ['as' => 'api.extra.account-pdf', 'uses' => 'PdfController@accountPdf']);
        ApiRoute::post('account-csv', ['as' => 'api.extra.account-csv', 'uses' => 'PdfController@exportAccountEntries']);
        ApiRoute::post('account-xlsx', ['as' => 'api.extra.account-xlsx', 'uses' => 'PdfController@downloadAccountEntriesExcel']);
        ApiRoute::post('payroll-pdf/{xid}', ['as' => 'api.extra.payroll-pdf', 'uses' => 'PdfController@payrollPdf']);

        ApiRoute::post('dashboard', ['as' => 'api.extra.dashboard', 'uses' => 'AuthController@dashboard']);
        ApiRoute::post('upload-file', ['as' => 'api.extra.upload-file', 'uses' => 'AuthController@uploadFile']);
        ApiRoute::post('profile', ['as' => 'api.extra.profile', 'uses' => 'AuthController@profile']);
        ApiRoute::post('user', ['as' => 'api.extra.user', 'uses' => 'AuthController@user']);
        ApiRoute::get('timezones', ['as' => 'api.extra.user', 'uses' => 'AuthController@getAllTimezones']);
        ApiRoute::get('get-all-employees', ['as' => 'api.extra.get-all-employees', 'uses' => 'AuthController@toGetAllEmployeeProfile']);

        // Dashboard
        ApiRoute::get('hrm/dashboard/today-attendance-users', ['as' => 'api.hrm.dashboard.today-attendance-users', 'uses' => 'HrmDashboardController@todayAttendanceUsers']);
        ApiRoute::get('hrm/dashboard/pending-leaves', ['as' => 'api.hrm.dashboard.pending-leaves', 'uses' => 'HrmDashboardController@pendingLeaves']);
        ApiRoute::get('hrm/dashboard/pending-expense', ['as' => 'api.hrm.dashboard.pending-expense', 'uses' => 'HrmDashboardController@pendingExpense']);
        ApiRoute::get('hrm/today-attendance-details', ['as' => 'api.hrm.dashboard.today_attendance', 'uses' => 'HrmDashboardController@getTodayAttendanceDetails']);
        ApiRoute::post('hrm/mark-attendance', ['as' => 'api.hrm.dashboard.mark_attendance', 'uses' => 'HrmDashboardController@markTodayAttendance']);
        ApiRoute::post('hrm/update-settings', ['as' => 'api.hrm.update-settings', 'uses' => 'HrmDashboardController@updateHrmSettings']);

        ApiRoute::resource('asset-users', 'AssetUserController', ['as' => 'api', 'only' => ['index']]);

        // Leaves will be visible to everyone
        // For other route controller level permission applied
        ApiRoute::post('add-users-attendance', ['as' => 'api.leaves.add-users-attendance', 'uses' => 'AttendanceController@addUserAttendance']);
        ApiRoute::post('leaves/update-status/{id}', ['as' => 'api.leaves.update-status', 'uses' => 'LeaveController@statusUpdate']);
        ApiRoute::post('expenses/update-status/{id}', ['as' => 'api.expenses.update-status', 'uses' => 'ExpenseController@changeStatus']);
        ApiRoute::get('leaves/remaining-leaves', ['as' => 'api.leaves.remaining-leaves', 'uses' => 'LeaveController@remainingLeaves']);
        ApiRoute::get('leaves/unpaid-leaves', ['as' => 'api.leaves.unpaid-leaves', 'uses' => 'LeaveController@unpaidLeaves']);
        ApiRoute::get('leaves/paid-leaves', ['as' => 'api.leaves.paid-leaves', 'uses' => 'LeaveController@paidLeaves']);
        ApiRoute::resource('leaves', 'LeaveController', ['as' => 'api']);
    });

    // Routes Accessable to thouse user who have permissions realted to route
    ApiRoute::group(['middleware' => ['api.permission.check', 'api.auth.check', 'license-expire']], function () {
        $options = [
            'as' => 'api',
            'except' => ['show']
        ];

        // Imports
        ApiRoute::post('users/import', ['as' => 'api.users.import', 'uses' => 'UsersController@import']);
        ApiRoute::post('attendances/import', ['as' => 'api.attendances.import', 'uses' => 'AttendanceController@import']);
        ApiRoute::post('users-static-csv', ['as' => 'api.extra.users-static-csv', 'uses' => 'PdfController@downloadUsersStaticCsv']);

        // Create Menu Update
        ApiRoute::post('companies/update-create-menu', ['as' => 'api.companies.update-create-menu', 'uses' => 'CompanyController@updateCreateMenu']);

        ApiRoute::resource('users', 'UsersController', $options);
        ApiRoute::post('update-salary', ['as' => 'api.users.basic_salary', 'uses' => 'UsersController@updateBasicSalary']);
        ApiRoute::post('change-group', ['as' => 'api.users.change_group', 'uses' => 'UsersController@changeGroup']);
        ApiRoute::resource('companies', 'CompanyController', ['as' => 'api', 'only' => ['update']]);
        ApiRoute::resource('permissions', 'PermissionController', ['as' => 'api', 'only' => ['index']]);
        ApiRoute::resource('roles', 'RolesController', $options);

        ApiRoute::get('attendances/summary-month', ['as' => 'api.attendances.summary-month', 'uses' => 'AttendanceController@attendanceSummaryByMonth']);
        ApiRoute::get('attendances/summary', ['as' => 'api.attendances.summary', 'uses' => 'AttendanceController@attendanceSummary']);
        ApiRoute::resource('attendances', 'AttendanceController', $options);

        ApiRoute::post('add-quick-holiday', ['as' => 'api.add-quick-holiday', 'uses' => 'HolidayController@addQuickHoliday']);
        ApiRoute::post('get-holiday-year-data', ['as' => 'api.get-holiday-year-data', 'uses' => 'HolidayController@getHolidayDataByYear']);
        ApiRoute::post('mark-holidays', ['as' => 'api.mark-holidays', 'uses' => 'HolidayController@markHoliday']);
        ApiRoute::resource('holidays', 'HolidayController', $options);

        ApiRoute::resource('shifts', 'ShiftController', $options);
        ApiRoute::resource('departments', 'DepartmentController', $options);
        ApiRoute::resource('designations', 'DesignationController', $options);
        ApiRoute::resource('leave-types', 'LeaveTypeController', $options);
        ApiRoute::resource('awards', 'AwardController', $options);
        ApiRoute::resource('appreciations', 'AppreciationController', $options);
        ApiRoute::resource('increments-promotions', 'IncrementPromotionController', $options);
        ApiRoute::resource('payrolls', 'PayrollController',  $options);
        ApiRoute::resource('pre-payments', 'PrePaymentController', $options);
        ApiRoute::resource('asset-types', 'AssetTypeController', $options);
        ApiRoute::post('forms/update-status', ['as' => 'api.forms.update-status', 'uses' => 'FormController@updateStatus']);


        ApiRoute::post('payrolls/generate', ['as' => 'api.payrolls.generate', 'uses' => 'PayrollController@payrollGenerate']);
        ApiRoute::post('payrolls/update-status', ['as' => 'api.payrolls.update-status', 'uses' => 'PayrollController@updateStatus']);
        ApiRoute::resource('expenses', 'ExpenseController', $options);
        ApiRoute::resource('expense-categories', 'ExpenseCategoryController', $options);
        ApiRoute::resource('locations', 'LocationController', $options);
        ApiRoute::resource('email-templates', 'EmailTemplateController', ['as' => 'api', 'only' => ['index', 'update']]);
        ApiRoute::resource('letter-head-templates', 'LetterHeadTemplateController', $options);
        ApiRoute::resource('generates', 'GenerateController', ['as' => 'api']);
        ApiRoute::resource('assets', 'AssetController', $options);
        ApiRoute::resource('get-all-transitions', 'AccountEntryController', ['as' => 'api', 'only' => ['index']]);


        ApiRoute::resource('complaints', 'ComplaintController', $options);
        ApiRoute::resource('news', 'NewsController', $options);
        ApiRoute::resource('forms', 'FormController', $options);
        ApiRoute::resource('feedbacks', 'FeedbackController', $options);
        ApiRoute::post('create-feedback-user-rating', ['as' => 'api.create-feedback-user-rating', 'uses' => 'FeedbackController@createFeedbackRating']);
        ApiRoute::resource('company-policies', 'CompanyPolicyController', $options);
        ApiRoute::resource('accounts', 'AccountController', $options);
        ApiRoute::resource('payees', 'PayeeController', $options);
        ApiRoute::resource('payers', 'PayerController', $options);
        ApiRoute::resource('deposit-categories', 'DepositCategoryController', $options);
        ApiRoute::resource('deposits', 'DepositController', $options);
        ApiRoute::resource('warnings', 'WarningController', $options);
        ApiRoute::resource('resignations', 'ResignationController', $options);
        ApiRoute::resource('terminations', 'TerminationController', $options);
        ApiRoute::resource('indicators', 'IndicatorController', $options);
        ApiRoute::resource('salary-components', 'SalaryComponentController', $options);
        ApiRoute::resource('salary-groups', 'SalaryGroupController', $options);
        ApiRoute::resource('pdf-fonts', 'PdfFontsController', $options);
        ApiRoute::post('resignation-change-status', ['as' => 'api.resignation-change-status', 'uses' => 'ResignationController@updateResignationStatus']);
        ApiRoute::post('add-asset-to-lend', ['as' => 'api.assets.add-asset-to-lend', 'uses' => 'AssetController@addAssetToLend']);
        ApiRoute::post('remove-lent-asset', ['as' => 'api.assets.remove-lent-asset', 'uses' => 'AssetController@removeLendAsset']);
        ApiRoute::delete('remove-asset-user/{id}', ['as' => 'api.assets.remove-asset-user', 'uses' => 'AssetController@removAssetUser']);
        ApiRoute::post('news-publish/{id}', ['as' => 'api.news.news-publish', 'uses' => 'NewsController@publishNews']);
        ApiRoute::get('employee-survey/{id}', ['as' => 'api.feedbacks.employee-survey', 'uses' => 'FeedbackController@toGetFeedback']);
        ApiRoute::get('employee-profile/{id}', ['as' => 'api.employees.employee-profile', 'uses' => 'UsersController@toGetEmployeeProfile']);
        ApiRoute::post('employee-feedback', ['as' => 'api.feedbacks.employee-feedback', 'uses' => 'FeedbackController@toSetEmployeeFeedback']);
        ApiRoute::resource('get-assigned-survey', 'FeedbackUserController', ['as' => 'api', 'only' => ['index']]);
        ApiRoute::post('get-employee-responsed', ['as' => 'api.feedbacks.get-employee-responsed', 'uses' => 'FeedbackController@toGetEmployeeResponse']);
        ApiRoute::post('change-status', ['as' => 'api.change-status', 'uses' => 'ComplaintController@changeStatus']);
        ApiRoute::post('update-expense-status/{id}', ['as' => 'api.update-expense-status', 'uses' => 'ExpenseController@changeStatus']);

        ApiRoute::get('filter-user', ['as' => 'api.salary-group.filter-user', 'uses' => 'SalaryGroupController@filterUser']);
        ApiRoute::get('disabled-value/{id}', ['as' => 'api.salary-component.disabled-value', 'uses' => 'SalaryComponentController@disabledValue']);
        ApiRoute::post('pdf-custom-fonts', ['as' => 'api.pdf-custom-fonts', 'uses' => 'CompanyController@updatePdfFontSetting']);
        ApiRoute::resource('employee-work-status', 'EmployeeWorkStatusController', $options);
        ApiRoute::resource('field-types', 'FieldTypesController', $options);
        ApiRoute::get('employee-fields', ['as' => 'api.employee-fields', 'uses' => 'EmployeeFieldsController@getEmployeeFieldsData']);
        ApiRoute::post('update-visible-to-employee', ['as' => 'api.update-visible-to-employee', 'uses' => 'FieldTypesController@updateStatus']);
        ApiRoute::post('employee-specific-leave', ['as' => 'api.employee-specific-leave', 'uses' => 'LeaveTypeController@storeUpdateEmployeeSpecificLeave']);
    });
});
