<?php

use Examyou\RestAPI\Facades\ApiRoute;

// Self Routes
ApiRoute::group(['prefix' => 'self', 'namespace' => 'App\Http\Controllers\Api\Self', 'middleware' => ['api.auth.check', 'license-expire']], function () {
    $options = [
        'as' => 'api',
        'only' => ['index']
    ];

    ApiRoute::post('dashboard', ['as' => 'api.self.dashboard', 'uses' => 'DashboardController@summary']);

    // Attendance Route
    ApiRoute::get('summary-month', ['as' => 'api.self.summary-month', 'uses' => 'AttendantController@attendanceSummaryByMonth']);
    ApiRoute::get('summary', ['as' => 'api.self.summary', 'uses' => 'AttendantController@attendanceSummary']);

    // Leave Routes
    ApiRoute::get('remaining-leaves', ['as' => 'api.self.remaining-leaves', 'uses' => 'LeaveController@remainingLeaves']);
    ApiRoute::get('leave-types', ['as' => 'api.self.leave-types', 'uses' => 'LeaveController@getLeavesType']);
    ApiRoute::get('unpaid-leaves', ['as' => 'api.self.unpaid-leaves', 'uses' => 'LeaveController@unpaidLeaves']);
    ApiRoute::get('paid-leaves', ['as' => 'api.self.paid-leaves', 'uses' => 'LeaveController@paidLeaves']);
    ApiRoute::get('filter-leaves', ['as' => 'api.leaves.filter-leaves', 'uses' => 'LeaveController@filterLeaves']);


    ApiRoute::get('increments-promotions', ['as' => 'api.increments-promotions.index', 'uses' => 'IncrementPromotionController@index']);
    ApiRoute::get('payrolls', ['as' => 'api.payrolls.index', 'uses' => 'PayrollController@index']);
    ApiRoute::get('pre-payments', ['as' => 'api.pre-payments.index', 'uses' => 'PrePaymentController@index']);
    ApiRoute::post('employee-feedback', ['as' => 'api.employee-feedback', 'uses' => 'FeedbackUserController@toSetEmployeeFeedback']);
    ApiRoute::get('users', ['as' => 'api.self.users.index', 'uses' => 'UsersController@index']);
    ApiRoute::get('get-user-documents', ['as' => 'api.self.get-user-documents.index', 'uses' => 'UsersController@getUserDocument']);
    ApiRoute::get('field-types', ['as' => 'api.self.field-types.index', 'uses' => 'UsersController@getFieldTypes']);

    ApiRoute::get('letter-head-templates', ['as' => 'api.self.letter-head-templates.index', 'uses' => 'LetterHeadsController@getLetterHeadTemplates']);
    ApiRoute::resource('generates', 'LetterHeadsController', $options);

    ApiRoute::get('expense-categories', ['as' => 'api.self.expense-categories.index', 'uses' => 'ExpenseController@getExpenseCategories']);
    ApiRoute::resource('expenses', 'ExpenseController', ['as' => 'api']);

    ApiRoute::get('awards', ['as' => 'api.self.awards.index', 'uses' => 'AppreciationController@getAwards']);
    ApiRoute::resource('appreciations', 'AppreciationController', $options);

    // Option Resource Routes
    ApiRoute::resource('holidays', 'HolidayController', $options);
    ApiRoute::resource('assets', 'AssetController', $options);
    ApiRoute::resource('company-policies', 'CompanyPolicyController', $options);
    ApiRoute::resource('news', 'NewsController', $options);
    ApiRoute::resource('get-assigned-survey', 'FeedbackUserController', $options);
    ApiRoute::resource('warnings', 'WarningController', $options);

    ApiRoute::resource('complaints', 'ComplaintController', ['as' => 'api']);
    ApiRoute::resource('leaves', 'LeaveController', ['as' => 'api']);
    ApiRoute::resource('resignations', 'ResignationController', ['as' => 'api']);
    ApiRoute::get('locations', ['as' => 'api.self.locations.index', 'uses' => 'OpeningsController@getLocations']);
});
