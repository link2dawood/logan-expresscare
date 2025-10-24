<?php

namespace App\Classes;

use App\Models\Appreciation;
use App\Models\AssetUser;
use App\Models\Attendance;
use App\Models\CompanyPolicy;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\FeedbackUser;
use App\Models\Leave;
use App\Models\Offboarding;
use App\Models\Payroll;
use App\Models\Settings;
use App\Models\User;
use App\Models\Warning;
use App\Notifications\MainNotificaiton;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;

class Notify
{
    public static function getData($sendFor, $sendData)
    {
        $data = [];

        if (in_array($sendFor, ['employee_welcome_mail'])) {
            $user = User::with(['company', 'department', 'designation', 'shift'])->where('id', $sendData->id)->first();
            $user->user_password =  isset($sendData->user_password) ? $sendData->user_password : '';

            $data['to'] = $user;
            $data['company'] = $user->company;
            $data['user'] = $user;
        } else if (in_array($sendFor, ['employee_asset_lent', 'employee_asset_return'])) {
            $assetUser = AssetUser::with(['user', 'user.company', 'asset', 'lentBy', 'returnBy'])->where('id', $sendData->id)->first();

            $data['to'] = $assetUser->user;
            $data['user'] = $assetUser->user;
            $data['company'] = $assetUser->user->company;
            $data['asset'] = $assetUser->asset;
            $data['assetUser'] = $assetUser;
        } else if (in_array($sendFor, ['employee_on_appreciation'])) {
            $appreciation = Appreciation::with(['user', 'user.company', 'award'])->where('id', $sendData->id)->first();

            $data['to'] = $appreciation->user;
            $data['user'] = $appreciation->user;
            $data['company'] = $appreciation->user->company;
            $data['award'] = $appreciation->award;
            $data['appreciation'] = $appreciation;
        } else if (in_array($sendFor, ['employee_leave_approve', 'employee_leave_reject'])) {
            $leave = Leave::with(['user', 'user.company', 'leaveType'])->where('id', $sendData->id)->first();

            $data['to'] = $leave->user;
            $data['user'] = $leave->user;
            $data['company'] = $leave->user->company;
            $data['leaveType'] = $leave->leaveType;
            $data['leave'] = $leave;
        } else if (in_array($sendFor, ['employee_news_published'])) {
            $newsUser = User::with(['company'])->where('id', $sendData['user_id'])->first();

            $data['to'] = $newsUser;
            $data['user'] = $newsUser;
            $data['company'] = $newsUser->company;
            $data['news'] = $sendData['news'];
        } else if (in_array($sendFor, ['employee_payroll_paid'])) {
            $payroll = Payroll::with(['user', 'user.company'])->where('id', $sendData->id)->first();

            $data['to'] = $payroll->user;
            $data['user'] = $payroll->user;
            $data['company'] = $payroll->user->company;
            $data['payroll'] = $payroll;
        } else if (in_array($sendFor, ['employee_company_policies_create', 'employee_company_policies_update'])) {
            $companyPolicy = CompanyPolicy::find($sendData->company_policy_id);
            $user = User::with(['company'])->where('id', $sendData->id)->first();

            $data['to'] = $user;
            $data['user'] = $user;
            $data['company'] = $user->company;
            $data['companyPolicy'] = $companyPolicy;
        } else if (in_array($sendFor, ['employee_warning'])) {
            $warning = Warning::with(['user', 'user.company'])->where('id', $sendData->id)->first();

            $data['to'] = $warning->user;
            $data['user'] = $warning->user;
            $data['company'] = $warning->user->company;
            $data['warning'] = $warning;
        } else if (in_array($sendFor, ['employee_terminations'])) {
            $offboarding = Offboarding::with(['user', 'user.company'])->where('offboardings.type', 'termination')->where('id', $sendData->id)->first();

            $data['to'] = $offboarding->user;
            $data['user'] = $offboarding->user;
            $data['company'] = $offboarding->user->company;
            $data['termination'] = $offboarding;
        } else if (in_array($sendFor, ['employee_resignation_approve', 'employee_resignation_reject'])) {
            $offboarding = Offboarding::with(['user', 'user.company'])->where('offboardings.type', 'resignation')->where('id', $sendData->id)->first();

            $data['to'] = $offboarding->user;
            $data['user'] = $offboarding->user;
            $data['company'] = $offboarding->user->company;
            $data['resignation'] = $offboarding;
        } else if (in_array($sendFor, ['employee_complaint_approve', 'employee_complaint_reject'])) {
            $complaint = Complaint::with(['toStaff', 'toStaff.company', 'fromStaff'])->where('id', $sendData->id)->first();

            $data['to'] = $complaint->fromStaff;
            $data['user'] = $complaint->fromStaff;
            $data['company'] = $complaint->fromStaff->company;
            $data['complaint'] = $complaint;
        } else if (in_array($sendFor, ['employee_expense_approve', 'employee_expense_reject'])) {
            $expense = Expense::with(['user', 'user.company', 'expenseCategory'])->where('id', $sendData->id)->first();

            $data['to'] = $expense->user;
            $data['user'] = $expense->user;
            $data['company'] = $expense->user->company;
            $data['expense'] = $expense;
            $data['expenseCategory'] = $expense->expenseCategory;
        } else if (in_array($sendFor, ['employee_survey_forms_assign'])) {
            $newsUser = FeedbackUser::with(['user', 'user.company', 'feedback'])->where('id', $sendData->id)->first();

            $data['to'] = $newsUser->user;
            $data['user'] = $newsUser->user;
            $data['company'] = $newsUser->user->company;
            $data['survey'] = $newsUser->feedback;
        }

        // For Company related notifications
        else if (in_array($sendFor, ['company_employee_leave_apply'])) {
            $leave = Leave::with(['user', 'user.company', 'leaveType'])->where('id', $sendData->id)->first();

            $data['to'] = $leave->user->company;
            $data['user'] = $leave->user;
            $data['company'] = $leave->user->company;
            $data['leaveType'] = $leave->leaveType;
            $data['leave'] = $leave;
        } else if (in_array($sendFor, ['company_employee_clock_in', 'company_employee_clock_out'])) {
            $attendance = Attendance::with(['user', 'user.company'])->where('id', $sendData->id)->first();

            $data['to'] = $attendance->user->company;
            $data['user'] = $attendance->user;
            $data['company'] = $attendance->user->company;
            $data['attendance'] = $attendance;
        } else if (in_array($sendFor, ['company_employee_resignation_apply'])) {
            $offboarding = Offboarding::with(['user', 'user.company'])->where('offboardings.type', 'resignation')->where('id', $sendData->id)->first();

            $data['to'] = $offboarding->user->company;
            $data['user'] = $offboarding->user;
            $data['company'] = $offboarding->user->company;
            $data['resignation'] = $offboarding;
        } else if (in_array($sendFor, ['company_employee_complaint_apply'])) {
            $complaint = Complaint::with(['fromStaff', 'fromStaff.company', 'toStaff'])->where('id', $sendData->id)->first();

            $data['to'] = $complaint->fromStaff->company;
            $data['user'] = $complaint->fromStaff;
            $data['company'] = $complaint->fromStaff->company;
            $data['complaint'] = $complaint;
        } else if (in_array($sendFor, ['company_employee_expense_apply'])) {
            $expense = Expense::with(['user', 'user.company', 'expenseCategory'])->where('id', $sendData->id)->first();

            $data['to'] = $expense->user->company;
            $data['user'] = $expense->user;
            $data['company'] = $expense->user->company;
            $data['expense'] = $expense;
            $data['expenseCategory'] = $expense->expenseCategory;
        } else if (in_array($sendFor, ['company_employee_survey_submit'])) {
            $newsUser = FeedbackUser::with(['user', 'user.company', 'feedback'])->where('id', $sendData->id)->first();

            $data['to'] = $newsUser->user->company;
            $data['user'] = $newsUser->user;
            $data['company'] = $newsUser->user->company;
            $data['survey'] = $newsUser->feedback;
        }

        return $data;
    }

    public static function isAbleToSendMail($sendFor, $dataArray)
    {
        $isAbleToSendMail = false;
        $content = "";
        $title = "";

        if (app_type() == 'saas') {
            $globalCompany = \App\SuperAdmin\Models\GlobalCompany::first();
            $mailSetting = DB::table('settings')->where('setting_type', 'email')
                ->where('name_key', 'smtp')
                ->where('is_global', 1)
                ->where('company_id', $globalCompany->id)
                ->first();
        } else {
            $mailSetting = Settings::where('setting_type', 'email')
                ->where('name_key', 'smtp')
                ->first();
        }


        // For Email
        if ($mailSetting && $mailSetting->status && $mailSetting->verified) {

            $sendMailSettings = Settings::where('setting_type', 'send_mail_settings')
                ->first();

            if ($sendMailSettings && $sendMailSettings->credentials && in_array($sendFor, $sendMailSettings->credentials)) {
                $isAbleToSendMail = true;

                // Retrieve $content and $title from database using $sendFor
                $notificationSetting = Settings::where('setting_type', 'email_templates')
                    ->where('name_key', $sendFor)
                    ->first();

                if (!$notificationSetting) {
                    throw new ApiException('No email template found for ' . $sendFor);
                }

                $title = $notificationSetting && $notificationSetting->credentials ? $notificationSetting->credentials['title'] : '';
                $content = $notificationSetting && $notificationSetting->credentials ? $notificationSetting->credentials['content'] : '';

                $title = self::getReplacedHtml($title, $dataArray);
                $content = self::getReplacedHtml($content, $dataArray);
            }
        }

        return [
            'setting' => $mailSetting,
            'isAbleToSend' => $isAbleToSendMail,
            'content' => $content,
            'title' => $title
        ];
    }

    public static function getDataArray($data)
    {
        $newDataKey = [
            'EMPLOYEE_NAME' => $data && isset($data['user']) && isset($data['user']['name']) ? $data['user']['name'] : '',
            'EMPLOYEE_EMAIL' => $data && isset($data['user']) && isset($data['user']['email']) ? $data['user']['email'] : '',
            'EMPLOYEE_PASSWORD' => $data && isset($data['user']) && isset($data['user']['user_password']) ? $data['user']['user_password'] : '',
            'EMPLOYEE_ID' => $data && isset($data['user']) && isset($data['user']['employee_number']) ? $data['user']['employee_number'] : '',
            'EMPLOYEE_JOINING_DATE' => $data && isset($data['user']) && isset($data['user']['joining_date']) ? $data['user']['joining_date'] : '',
            'EMPLOYEE_DEPARTMENT' => $data && isset($data['user']) && isset($data['user']['department']) && isset($data['user']['department']['name']) ? $data['user']['department']['name'] : '',
            'EMPLOYEE_DESIGNATION' => $data && isset($data['user']) && isset($data['user']['designation']) && isset($data['user']['designation']['name']) ? $data['user']['designation']['name'] : '',
            'EMPLOYEE_SHIFT' => $data && isset($data['user']) && isset($data['user']['shift']) && isset($data['user']['shift']['name']) ? $data['user']['shift']['name'] : '',

            'ASSET_NAME' => $data && isset($data['asset']) && isset($data['asset']['name']) ? $data['asset']['name'] : '',
            'ASSET_SERIAL_NUMBER' => $data && isset($data['asset']) && isset($data['asset']['serial_number']) ? $data['asset']['serial_number'] : '',
            'ASSET_STATUS' => $data && isset($data['asset']) && isset($data['asset']['status']) ? $data['asset']['status'] : '',
            'ASSET_LENT_DATE' => $data && isset($data['assetUser']) && isset($data['assetUser']['lend_date']) ? $data['assetUser']['lend_date'] : '',
            'ASSET_RETURN_DATE' => $data && isset($data['assetUser']) && isset($data['assetUser']['return_date']) ? $data['assetUser']['return_date'] : '',
            'ASSET_ACTUAL_RETURN_DATE' => $data && isset($data['assetUser']) && isset($data['assetUser']['actual_return_date']) ? $data['assetUser']['actual_return_date'] : '',
            'ASSET_LENT_BY' => $data && isset($data['assetUser']) && isset($data['assetUser']['lentBy']) && isset($data['assetUser']['lentBy']['name']) ? $data['assetUser']['lentBy']['name'] : '',
            'ASSET_RETURN_BY' => $data && isset($data['assetUser']) && isset($data['assetUser']['returnBy']) && isset($data['assetUser']['returnBy']['name']) ? $data['assetUser']['returnBy']['name'] : '',

            'APPRECIATION_DESCRIPTION' => $data && isset($data['appreciation']) && isset($data['appreciation']['description']) ? $data['appreciation']['description'] : '',
            'APPRECIATION_AMOUNT' => $data && isset($data['appreciation']) && isset($data['appreciation']['price_amount']) ? $data['appreciation']['price_amount'] : '',
            'AWARD_NAME' => $data && isset($data['award']) && isset($data['award']['name']) ? $data['award']['name'] : '',


            'LEAVE_TYPE_NAME' => $data && isset($data['leaveType']) && isset($data['leaveType']['name']) ? $data['leaveType']['name'] : '',
            'LEAVE_START_DATE' => $data && isset($data['leave']) && isset($data['leave']['start_date']) ? $data['leave']['start_date'] : '',
            'LEAVE_END_DATE' => $data && isset($data['leave']) && isset($data['leave']['end_date']) ? $data['leave']['end_date'] : '',
            'LEAVE_TOTAL_DAYS' => $data && isset($data['leave']) && isset($data['leave']['total_days']) ? $data['leave']['total_days'] : '',
            'LEAVE_REASON' => $data && isset($data['leave']) && isset($data['leave']['reason']) ? $data['leave']['reason'] : '',

            'NEWS_TITLE' => $data && isset($data['news']) && isset($data['news']['title']) ? $data['news']['title'] : '',

            'EXPENSE_CATEGORY' => $data && isset($data['expenseCategory']) && isset($data['expenseCategory']['name']) ? $data['expenseCategory']['name'] : '',

            'EXPENSE_AMOUNT' => $data && isset($data['expense']) && isset($data['expense']['amount']) ? $data['expense']['amount'] : '',
            'EXPENSE_DESCRIPTION' => $data && isset($data['expense']) && isset($data['expense']['notes']) ? $data['expense']['notes'] : '',
            'EXPENSE_DATE' => $data && isset($data['expense']) && isset($data['expense']['date_time']) ? $data['expense']['date_time'] : '',

            'PAYROLL_MONTH' => $data && isset($data['payroll']) && isset($data['payroll']['month']) ? $data['payroll']['month'] : '',
            'PAYROLL_YEAR' => $data && isset($data['payroll']) && isset($data['payroll']['year']) ? $data['payroll']['year'] : '',
            'PAYROLL_NET_SALARY' => $data && isset($data['payroll']) && isset($data['payroll']['salary_amount']) ? $data['payroll']['salary_amount'] : '',

            'COMPANY_POLICY_TITLE' => $data && isset($data['companyPolicy']) && isset($data['companyPolicy']['title']) ? $data['companyPolicy']['title'] : '',

            'WARNING_TITLE' => $data && isset($data['warning']) && isset($data['warning']['title']) ? $data['warning']['title'] : '',

            'RESIGNATION_DATE' => $data && isset($data['resignation']) && isset($data['resignation']['start_date']) ? $data['resignation']['start_date'] : '',
            'RESIGNATION_LAST_WORKING_DATE' => $data && isset($data['resignation']) && isset($data['resignation']['end_date']) ? $data['resignation']['end_date'] : '',
            'RESIGNATION_REASON' => $data && isset($data['resignation']) && isset($data['resignation']['description']) ? $data['resignation']['description'] : '',
            'RESIGNATION_REPLY_NOTE' => $data && isset($data['resignation']) && isset($data['resignation']['reply_notes']) ? $data['resignation']['reply_notes'] : '',

            'TERMINATION_DATE' => $data && isset($data['termination']) && isset($data['termination']['start_date']) ? $data['termination']['start_date'] : '',
            'TERMINATION_LAST_WORKING_DATE' => $data && isset($data['termination']) && isset($data['termination']['end_date']) ? $data['termination']['end_date'] : '',
            'TERMINATION_TITLE' => $data && isset($data['termination']) && isset($data['termination']['title']) ? $data['termination']['title'] : '',
            'TERMINATION_DESCRIPTION' => $data && isset($data['termination']) && isset($data['termination']['description']) ? $data['termination']['description'] : '',

            'COMPLAINT_AGAINST' => $data && isset($data['complaint']) && isset($data['complaint']['toStaff']) && isset($data['complaint']['toStaff']['name']) ? $data['complaint']['toStaff']['name'] : '',
            'COMPLAINT_FROM' => $data && isset($data['complaint']) && isset($data['complaint']['fromStaff']) && isset($data['complaint']['fromStaff']['name']) ? $data['complaint']['fromStaff']['name'] : '',
            'COMPLAINT_DATE' => $data && isset($data['complaint']) && isset($data['complaint']['date_time']) ? $data['complaint']['date_time'] : '',
            'COMPLAINT_TITLE' => $data && isset($data['complaint']) && isset($data['complaint']['title']) ? $data['complaint']['title'] : '',
            'COMPLAINT_REPLY_NOTES' => $data && isset($data['complaint']) && isset($data['complaint']['reply_notes']) ? $data['complaint']['reply_notes'] : '',
            'COMPLAINT_DESCRIPTION' => $data && isset($data['complaint']) && isset($data['complaint']['description']) ? $data['complaint']['description'] : '',

            'SURVEY_LAST_DATE' => $data && isset($data['survey']) && isset($data['survey']['last_date']) ? $data['survey']['last_date'] : '',
            'SURVEY_TITLE' => $data && isset($data['survey']) && isset($data['survey']['title']) ? $data['survey']['title'] : '',
            'SURVEY_DESCRIPTION' => $data && isset($data['survey']) && isset($data['survey']['description']) ? $data['survey']['description'] : '',

            'ATTENDANCE_CLOCK_IN_TIME' => $data && isset($data['attendance']) && isset($data['attendance']['clock_in_date_time']) ? $data['attendance']['clock_in_date_time'] : '',
            'ATTENDANCE_CLOCK_IN_IP' => $data && isset($data['attendance']) && isset($data['attendance']['clock_in_ip_address']) ? $data['attendance']['clock_in_ip_address'] : '',
            'ATTENDANCE_CLOCK_OUT_TIME' => $data && isset($data['attendance']) && isset($data['attendance']['clock_out_date_time']) ? $data['attendance']['clock_out_date_time'] : '',
            'ATTENDANCE_CLOCK_OUT_IP' => $data && isset($data['attendance']) && isset($data['attendance']['clock_out_ip_address']) ? $data['attendance']['clock_out_ip_address'] : '',

            'LOGIN_URL' => route('main', 'admin/login'),
            'COMPANY_NAME' => $data && isset($data['company']) && isset($data['company']['name']) ? $data['company']['name'] : '',
        ];

        return $newDataKey;
    }

    public static function mailVariables()
    {
        return [
            'employee_welcome_mail' => [
                'EMPLOYEE_NAME',
                'EMPLOYEE_EMAIL',
                'EMPLOYEE_PASSWORD',
                'EMPLOYEE_ID',
                'EMPLOYEE_JOINING_DATE',
                'EMPLOYEE_DEPARTMENT',
                'EMPLOYEE_DESIGNATION',
                'COMPANY_NAME',
                'LOGIN_URL',
            ],
            'employee_asset_lent' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'ASSET_NAME', 'ASSET_SERIAL_NUMBER', 'ASSET_STATUS', 'ASSET_LENT_DATE', 'ASSET_RETURN_DATE', 'ASSET_LENT_BY'],
            'employee_asset_return' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'ASSET_NAME', 'ASSET_SERIAL_NUMBER', 'ASSET_STATUS', 'ASSET_LENT_DATE', 'ASSET_RETURN_DATE', 'ASSET_ACTUAL_RETURN_DATE', 'ASSET_RETURN_BY'],
            'employee_on_appreciation' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'AWARD_NAME', 'APPRECIATION_DESCRIPTION', 'APPRECIATION_AMOUNT'],
            'employee_leave_approve' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'LEAVE_TYPE_NAME', 'LEAVE_START_DATE', 'LEAVE_END_DATE', 'LEAVE_TOTAL_DAYS', 'LEAVE_REASON'],
            'employee_leave_reject' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'LEAVE_TYPE_NAME', 'LEAVE_START_DATE', 'LEAVE_END_DATE', 'LEAVE_TOTAL_DAYS', 'LEAVE_REASON'],
            'employee_news_published' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'NEWS_TITLE'],
            'employee_payroll_paid' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'PAYROLL_MONTH', 'PAYROLL_YEAR', 'PAYROLL_NET_SALARY', 'EMPLOYEE_ID'],
            'employee_company_policies_create' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'COMPANY_POLICY_TITLE'],
            'employee_company_policies_update' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'COMPANY_POLICY_TITLE'],
            'employee_warning' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'WARNING_TITLE'],
            'employee_terminations' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'TERMINATION_LAST_WORKING_DATE', 'TERMINATION_DATE', 'TERMINATION_TITLE', 'TERMINATION_DESCRIPTION'],
            'employee_resignation_approve' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'RESIGNATION_LAST_WORKING_DATE', 'RESIGNATION_DATE', 'RESIGNATION_REASON', 'RESIGNATION_REPLY_NOTE'],
            'employee_resignation_reject' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'RESIGNATION_LAST_WORKING_DATE', 'RESIGNATION_DATE', 'RESIGNATION_REASON', 'RESIGNATION_REPLY_NOTE'],
            'employee_complaint_approve' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'COMPLAINT_DATE', 'COMPLAINT_TITLE', 'COMPLAINT_DESCRIPTION', 'COMPLAINT_REPLY_NOTES', 'COMPLAINT_AGAINST', 'COMPLAINT_FROM'],
            'employee_complaint_reject' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID',  'COMPLAINT_DATE', 'COMPLAINT_TITLE', 'COMPLAINT_DESCRIPTION', 'COMPLAINT_REPLY_NOTES', 'COMPLAINT_AGAINST', 'COMPLAINT_FROM'],
            'employee_expense_approve' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'EXPENSE_CATEGORY', 'EXPENSE_AMOUNT', 'EXPENSE_DATE', 'EXPENSE_DESCRIPTION'],
            'employee_expense_reject' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'EXPENSE_CATEGORY', 'EXPENSE_AMOUNT', 'EXPENSE_DATE', 'EXPENSE_DESCRIPTION'],
            'employee_survey_forms_assign' => ['COMPANY_NAME', 'EMPLOYEE_NAME', 'EMPLOYEE_ID', 'SURVEY_TITLE', 'SURVEY_DESCRIPTION', 'SURVEY_LAST_DATE'],

            // Company related notifications
            'company_employee_leave_apply' => ['COMPANY_NAME', 'EMPLOYEE_ID', 'EMPLOYEE_NAME', 'LEAVE_TYPE_NAME', 'LEAVE_START_DATE', 'LEAVE_END_DATE', 'LEAVE_TOTAL_DAYS', 'LEAVE_REASON'],
            'company_employee_clock_in' => ['COMPANY_NAME', 'EMPLOYEE_ID', 'EMPLOYEE_NAME', 'ATTENDANCE_CLOCK_IN_TIME', 'ATTENDANCE_CLOCK_IN_IP'],
            'company_employee_clock_out' => ['COMPANY_NAME', 'EMPLOYEE_ID', 'EMPLOYEE_NAME', 'ATTENDANCE_CLOCK_OUT_TIME', 'ATTENDANCE_CLOCK_OUT_IP'],
            'company_employee_resignation_apply' => [
                'COMPANY_NAME',
                'EMPLOYEE_ID',
                'EMPLOYEE_NAME',
                'EMPLOYEE_DEPARTMENT',
                'EMPLOYEE_DESIGNATION',
                'RESIGNATION_DATE',
                'RESIGNATION_REASON'
            ],
            'company_employee_complaint_apply' => [
                'COMPANY_NAME',
                'EMPLOYEE_ID',
                'EMPLOYEE_NAME',
                'COMPLAINT_AGAINST',
                'COMPLAINT_TITLE',
                'COMPLAINT_DATE',
                'COMPLAINT_DESCRIPTION'
            ],
            'company_employee_expense_apply' => [
                'COMPANY_NAME',
                'EMPLOYEE_ID',
                'EMPLOYEE_NAME',
                'EXPENSE_CATEGORY',
                'EXPENSE_AMOUNT',
                'EXPENSE_DESCRIPTION',
                'EXPENSE_DATE'
            ],
            'company_employee_survey_submit' => [
                'COMPANY_NAME',
                'EMPLOYEE_ID',
                'EMPLOYEE_NAME',
                'SURVEY_TITLE',
                'SURVEY_DESCRIPTION'
            ],
        ];
    }

    public static function getReplacedHtml($html, $dataArray)
    {
        preg_match_all("/##(\w+)##/m", $html, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $match) {
                $html = str_replace('##' . $match . '##', $dataArray[$match], $html);
            }
        }

        return $html;
    }

    public static function send($sendFor, $sendData)
    {

        $data = self::getData($sendFor, $sendData);
        $dataArray = self::getDataArray($data);
        $sender = $data['to'];

        $notficationData = [
            'send_for' => $sendFor,
            'to' => $data['to'],
            'mail' => self::isAbleToSendMail($sendFor, $dataArray),
            'data' => $data,
        ];

        $sender->notify(new MainNotificaiton($notficationData));
    }
}
