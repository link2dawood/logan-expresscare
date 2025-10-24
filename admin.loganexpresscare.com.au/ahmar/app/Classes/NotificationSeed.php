<?php

namespace App\Classes;

use App\Models\Company;
use App\Models\Settings;
use App\Scopes\CompanyScope;

class NotificationSeed
{
    public static $emailNotificaiotnArray = [

        // Users
        'employee_welcome_mail' => [
            'type' => "email_templates",
            'title' => "Welcome to the Company!",
            'content' => <<<HTML
<p>Hello <strong>##EMPLOYEE_NAME##</strong>,</p>
<p>We are excited to welcome you to our team at <strong>##COMPANY_NAME##!</strong> We believe your skills and experience will be a valuable addition to our company.</p>
<p>Here’s a quick summary of your onboarding:</p>
<ul>
    <li><strong>Department:</strong> ##EMPLOYEE_DEPARTMENT##</li>
    <li><strong>Designation:</strong> ##EMPLOYEE_DESIGNATION##</li>
    <li><strong>Shift:</strong> ##EMPLOYEE_SHIFT##</li>
    <li><strong>Joining Date:</strong> ##EMPLOYEE_JOINING_DATE##</li>
    <li><strong>Employee ID:</strong> ##EMPLOYEE_ID##</li>
</ul>

<p>Below is the login details to company account:</p>
<ul>
    <li><strong>Email:</strong> ##EMPLOYEE_EMAIL##</li>
    <li><strong>Password:</strong> ##EMPLOYEE_PASSWORD##</li>
</ul>

<p>To get started, please log in to your employee portal using the button below:</p>
<p><a href="##LOGIN_URL##" rel="noopener noreferrer" target="_blank">Login to Portal</a> If you have any questions, feel free to reach out to HR.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_asset_lent' => [
            'type' => "email_templates",
            'title' => "Asset Handover Confirmation",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>This is to acknowledge that the following asset has been handed over to you by <strong>##ASSET_LENT_BY##</strong>:</p>

<ul>
    <li><strong>Asset Name:</strong> ##ASSET_NAME##</li>
    <li><strong>Asset ID / Serial No:</strong> ##ASSET_SERIAL_NUMBER##</li>
    <li><strong>Condition:</strong> ##ASSET_STATUS##</li>
    <li><strong>Handover Date:</strong> ##ASSET_LENT_DATE##</li>
    <li><strong>Return Date:</strong> ##ASSET_RETURN_DATE##</li>
</ul>

<p>Please ensure proper usage and safekeeping of the asset as per company policy. You may be held responsible for any damage or loss during the lending period.</p>

<p>If you have any questions or need support regarding the asset, feel free to contact the IT/Admin department.</p>


<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_asset_return' => [
            'type' => "email_templates",
            'title' => "Asset Return Confirmation",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>This is to acknowledge that the following asset has been successfully returned by you to <strong>##ASSET_RETURN_BY##</strong>:</p>

<ul>
    <li><strong>Asset Name:</strong> ##ASSET_NAME##</li>
    <li><strong>Asset ID / Serial No:</strong> ##ASSET_SERIAL_NUMBER##</li>
    <li><strong>Condition at Return:</strong> ##ASSET_STATUS##</li>
    <li><strong>Return Date:</strong> ##ASSET_RETURN_DATE##</li>
    <li><strong>Actual Return Date:</strong> ##ASSET_ACTUAL_RETURN_DATE##</li>
</ul>

<p>Thank you for ensuring the asset was returned in <strong>##ASSET_STATUS##</strong> condition. This helps us maintain accurate inventory records and ensure accountability.</p>

<p>If you have any questions or need support regarding the asset, feel free to contact the IT/Admin department.</p>


<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_on_appreciation' => [
            'type' => "email_templates",
            'title' => "Congratulations on Your Appreciation!",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>We are pleased to inform you that you have been recognized and appreciated for your outstanding performance and contribution to our company.</p>

<p><strong>Award Name:</strong> ##AWARD_NAME##</p>

<p>##APPRECIATION_DESCRIPTION##</p>

<p>Your dedication, hard work, and positive attitude have not gone unnoticed. You continue to be an inspiration to your peers and a valuable asset to the team.</p>

<p>On behalf of all of us at <strong>##COMPANY_NAME##</strong>, congratulations once again. Keep up the amazing work!</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_leave_approve' => [
            'type' => "email_templates",
            'title' => "Your Leave Request Has Been Approved",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>We are pleased to inform you that your leave request has been <strong>approved</strong>.</p>

<p><strong>Leave Type:</strong> ##LEAVE_TYPE_NAME##</p>
<p><strong>From:</strong> ##LEAVE_START_DATE## &nbsp;&nbsp; <strong>To:</strong> ##LEAVE_END_DATE##</p>
<p><strong>Total Days:</strong> ##LEAVE_TOTAL_DAYS##</p>
<p><strong>Leave Reason:</strong> ##LEAVE_REASON##</p>

<p>Please ensure all responsibilities are managed before your leave begins, and keep in touch in case of any urgent requirements.</p>

<p>We hope you enjoy your time off and return refreshed.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_leave_reject' => [
            'type' => "email_templates",
            'title' => "Your Leave Request Has Been Rejected",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>We regret to inform you that your leave request has been <strong>rejected</strong> due to the following reason:</p>

<p><strong>Leave Type:</strong> ##LEAVE_TYPE_NAME##</p>
<p><strong>From:</strong> ##LEAVE_START_DATE## &nbsp;&nbsp; <strong>To:</strong> ##LEAVE_END_DATE##</p>
<p><strong>Total Days:</strong> ##LEAVE_TOTAL_DAYS##</p>
<p><strong>Leave Reason:</strong> ##LEAVE_REASON##</p>

<p>We understand this may be disappointing, and we encourage you to reach out if you have any concerns or if you'd like to reapply for a different date.</p>

<p>Thank you for your understanding.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_news_published' => [
            'type' => "email_templates",
            'title' => "New Company News Published",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>We’re excited to share a new update published within <strong>##COMPANY_NAME##</strong>!</p>

<p><strong>News Title:</strong> ##NEWS_TITLE##</p>

<p>Login to your company account and view the full news details.</p>

<p>We encourage you to stay informed and engaged with all company happenings. Your involvement is key to our collective success.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_payroll_paid' => [
            'type' => "email_templates",
            'title' => "Your Payroll Has Been Processed and Paid",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>We are pleased to inform you that your payroll for the period of <strong>##PAYROLL_MONTH## ##PAYROLL_YEAR##</strong> has been successfully <strong>processed and credited</strong> to your registered bank account.</p>

<p><strong>Employee ID:</strong> ##EMPLOYEE_ID##</p>
<p><strong>Pay Period:</strong> ##PAYROLL_MONTH## ##PAYROLL_YEAR##</p>
<p><strong>Net Salary:</strong> ##PAYROLL_NET_SALARY##</p>

<p>You can view or download your detailed salary slip by logging to your account.</p>

<p>If you have any questions regarding your payroll or deductions, please reach out to the HR or Accounts department.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_company_policies_create' => [
            'type' => "email_templates",
            'title' => "New Company Policy",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>We would like to inform you that a new company policy titled <strong>"##COMPANY_POLICY_TITLE##"</strong> has been officially created and published as part of our organizational guidelines.</p>

<p><strong>Policy Title:</strong> ##COMPANY_POLICY_TITLE</p>

<p>You are advised to read the policy thoroughly and ensure compliance. You can view or download the full policy document by login to your account:</p>

<p>If you have any questions or need further clarification, please feel free to contact the HR team.</p>

<p>Thank you for your attention and cooperation.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_company_policies_update' => [
            'type' => "email_templates",
            'title' => "Company Policy Updated",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>We would like to inform you that a company policy titled <strong>"##COMPANY_POLICY_TITLE##"</strong> has been revised and published as part of our organizational guidelines.</p>

<p><strong>Policy Title:</strong> ##COMPANY_POLICY_TITLE</p>

<p>You are advised to read the policy thoroughly and ensure compliance. You can view or download the full policy document by login to your account:</p>

<p>If you have any questions or need further clarification, please feel free to contact the HR team.</p>

<p>Thank you for your attention and cooperation.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_warning' => [
            'type' => "email_templates",
            'title' => "Official Warning Notice",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>This email serves as an official notice regarding a warning issued to you based on the following concern:</p>

<p><strong>Warning Title:</strong> ##WARNING_TITLE</p>

<p>Please consider this a formal communication intended to address the concern and provide an opportunity for improvement. We encourage you to reflect on the matter and take the necessary steps to align with company policies and expectations.</p>

<p>Continued non-compliance or recurrence of such behavior may lead to further disciplinary action, including suspension or termination as per company policy.</p>

<p>If you would like to discuss this further or need clarification, please feel free to reach out to your manager or the HR department.</p>

<p>We believe in fairness and growth, and we hope to see positive changes moving forward.</p>


<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_terminations' => [
            'type' => "email_templates",
            'title' => "Employment Termination Notice",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>This letter serves as formal notification that your employment with <strong>##COMPANY_NAME##</strong> will be terminated, effective <strong>##TERMINATION_LAST_WORKING_DATE##</strong>.</p>

<p><strong>Title for Termination:</strong> ##TERMINATION_TITLE##</p>
<p><strong>Reason for Termination:</strong> ##TERMINATION_DESCRIPTION##</p>

<p>This decision has been made after a thorough review and consideration of all relevant factors. Despite previous discussions and opportunities for improvement, the necessary standards and expectations have not been met, leading to this difficult but necessary step.</p>

<p>Please complete all offboarding procedures, including return of company property and clearance documentation, before your final working day. Your final settlement, including any outstanding dues (if applicable), will be processed in accordance with company policy.</p>

<p>If you have any questions or would like to discuss the matter further, you may reach out to the HR department.</p>

<p>We thank you for your efforts during your tenure and wish you the best for your future endeavors.</p>


<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_resignation_approve' => [
            'type' => "email_templates",
            'title' => "Resignation Acceptance Confirmation",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>This is to formally acknowledge and confirm that your resignation request, submitted on <strong>##RESIGNATION_DATE##</strong> has been <strong>approved</strong> and your last working date will be <strong>##RESIGNATION_LAST_WORKING_DATE##</strong>.</p>

<p>We thank you for your contributions during your time at <strong>##COMPANY_NAME##</strong>. Your dedication and efforts have been appreciated, and we wish you success in your future endeavors.</p>

<p>Kindly ensure that all handover procedures, clearance processes, and documentation are completed before your final working day. If you need any assistance during this transition, feel free to reach out to the HR department.</p>

<p>If applicable, your full and final settlement will be processed as per company policy.</p>

<p>We wish you the very best in your career ahead.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_resignation_reject' => [
            'type' => "email_templates",
            'title' => "Resignation Request Rejected",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>This is to inform you that your resignation request submitted on <strong>##RESIGNATION_DATE##</strong> has been reviewed and <strong>rejected</strong> by the management.</p>

<p><strong>Reason for Rejection:</strong> ##RESIGNATION_REPLY_NOTE##</p>

<p>We value your role and contributions to <strong>##COMPANY_NAME##</strong> and believe that continued collaboration will be beneficial for both you and the organization.</p>

<p>If you have any concerns or issues that led to your decision to resign, we encourage you to discuss them openly with your reporting manager or the HR department. We are willing to offer support and explore possible solutions.</p>

<p>Should you still wish to pursue this decision, kindly reach out for a further discussion.</p>


<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_complaint_approve' => [
            'type' => "email_templates",
            'title' => "Your Complaint Has Been Reviewed and Approved for Resolution",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>This is to inform you that your complaint submitted on <strong>##COMPLAINT_DATE##</strong> regarding <strong>##COMPLAINT_TITLE##</strong> has been <strong>reviewed and approved</strong> for further action.</p>

<p><strong>Complaint From:</strong> ##COMPLAINT_FROM##</p>
<p><strong>Complaint Against:</strong> ##COMPLAINT_AGAINST##</p>
<p><strong>Complaint Title:</strong> ##COMPLAINT_TITLE##</p>
<p><strong>Complaint Description:</strong> ##COMPLAINT_DESCRIPTION##</p>

<p>We appreciate you taking the time to report this matter. Our HR team has assessed the complaint and determined that it warrants formal resolution under the company’s grievance policy.</p>

<p>A dedicated representative will now begin addressing the issue, and you may be contacted shortly for any additional information or clarification if required.</p>

<p>Please be assured that your concerns are being handled confidentially and with care. We are committed to maintaining a safe, respectful, and supportive work environment for all employees.</p>

<p>If you have any further questions or would like to discuss this in more detail, feel free to reach out to the HR department.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_complaint_reject' => [
            'type' => "email_templates",
            'title' => "Complaint Request Rejected",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>This is to inform you that your complaint submitted on <strong>##COMPLAINT_DATE##</strong> regarding <strong>##COMPLAINT_TITLE##</strong> has been <strong>reviewed but will not be approved for further action at this time</strong>.</p>

<p><strong>Complaint From:</strong> ##COMPLAINT_FROM##</p>
<p><strong>Complaint Against:</strong> ##COMPLAINT_AGAINST##</p>
<p><strong>Complaint Title:</strong> ##COMPLAINT_TITLE##</p>
<p><strong>Complaint Description:</strong> ##COMPLAINT_DESCRIPTION##</p>

<p><strong>Reply Note:</strong> ##COMPLAINT_REPLY_NOTES##</p>

<p>We assure you that your complaint was reviewed thoroughly in accordance with company policy. Based on the available information, it was determined that the matter does not fall under a violation or actionable concern as defined by our internal procedures.</p>

<p>If you believe there is additional information or evidence that should be considered, you are welcome to reach out to the HR department for further discussion.</p>

<p>We appreciate your initiative in raising concerns and encourage you to continue using the proper channels whenever you feel uncomfortable or face issues at work.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_expense_approve' => [
            'type' => "email_templates",
            'title' => "Your Expense Request Has Been Approved",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>We are pleased to inform you that your expense reimbursement request submitted on <strong>##EXPENSE_DATE##</strong> has been <strong>approved</strong>.</p>

<p><strong>Expense Category:</strong> ##EXPENSE_CATEGORY##</p>
<p><strong>Expense Date:</strong> ##EXPENSE_DATE##</p>
<p><strong>Approved Amount:</strong> ##EXPENSE_AMOUNT##</p>
<p><strong>Expense Description:</strong> ##EXPENSE_DESCRIPTION##</p>

<p>The approved amount will be processed for reimbursement as per the standard payment schedule. You will receive a confirmation once the payment has been completed.</p>

<p>If you have any questions regarding the status of your reimbursement or need further clarification, please contact the finance team at ##COMPANY_NAME##.</p>

<p>Thank you for your timely submission and adherence to the expense policy.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_expense_reject' => [
            'type' => "email_templates",
            'title' => "Your Expense Request Has Been Rejected",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>We are pleased to inform you that your expense reimbursement request submitted on <strong>##EXPENSE_DATE##</strong> has been <strong>rejected</strong>.</p>

<p><strong>Expense Category:</strong> ##EXPENSE_CATEGORY##</p>
<p><strong>Expense Date:</strong> ##EXPENSE_DATE##</p>
<p><strong>Approved Amount:</strong> ##EXPENSE_AMOUNT##</p>
<p><strong>Expense Description:</strong> ##EXPENSE_DESCRIPTION##</p>

<p>We recommend reviewing the expense policy to ensure that all future claims meet the required criteria. If you believe this decision was made in error or have additional documentation to support your claim, you are welcome to contact the finance team for clarification or appeal.</p>

<p>If you have any questions regarding the status of your reimbursement or need further clarification, please contact the finance team at ##COMPANY_NAME##.</p>

<p>Thank you for your timely submission and adherence to the expense policy.</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],
        'employee_survey_forms_assign' => [
            'type' => "email_templates",
            'title' => "New Survey Now Available",
            'content' => <<<HTML
<p>Dear ##EMPLOYEE_NAME##,</p>

<p>We are pleased to invite you to participate in our latest survey. Your feedback is incredibly valuable and helps us improve the work environment, policies, and overall employee experience at <strong>##COMPANY_NAME##</strong>.</p>

<p><strong>Survey Title:</strong> ##SURVEY_TITLE##</p>
<p><strong>Survey Title:</strong> ##SURVEY_DESCRIPTION##</p>
<p><strong>Last Date:</strong> ##SURVEY_LAST_DATE##</p>

<p>Please take a few moments to complete the survey by login to your account.</p>

<p>Your responses will remain confidential, and the data will be used solely for improvement purposes.</p>

<p>Thank you in advance for your time and honest feedback!</p>

<p>Best regards,<br>
HR Team</p>
HTML,
            'other_data' => []
        ],

        // Company related notifications
        'company_employee_leave_apply' => [
            'type' => "email_templates",
            'title' => "New Leave Request Submitted by ##EMPLOYEE_NAME##",
            'content' => <<<HTML
<p>Hello,</p>

<p>This is to inform you that <strong>##EMPLOYEE_NAME##</strong> has submitted a new leave request.</p>

<p><strong>Employee Id:</strong> ##EMPLOYEE_ID##</p>
<p><strong>Employee Name:</strong> ##EMPLOYEE_NAME##</p>
<p><strong>Leave Type:</strong> ##LEAVE_TYPE_NAME##</p>
<p><strong>From:</strong> ##LEAVE_START_DATE## &nbsp;&nbsp; <strong>To:</strong> ##LEAVE_END_DATE##</p>
<p><strong>Total Days:</strong> ##LEAVE_TOTAL_DAYS##</p>
<p><strong>Reason:</strong> ##LEAVE_REASON##</p>

<p>You may review and take action on this request by login to your company account and by visiting the leave management system</p>

<p>For any urgent concerns, please contact the employee directly.</p>

HTML,
            'other_data' => []
        ],
        'company_employee_clock_in' => [
            'type' => "email_templates",
            'title' => "Employee Clock-In Notification - ##EMPLOYEE_NAME##",
            'content' => <<<HTML
<p>Hello,</p>

<p>This is to inform you that <strong>##EMPLOYEE_NAME##</strong> has successfully <strong>clocked in</strong> for the day.</p>

<p><strong>Employee Id:</strong> ##EMPLOYEE_ID##</p>
<p><strong>Employee Name:</strong> ##EMPLOYEE_NAME##</p>
<p><strong>Clock-In DateTime:</strong> ##ATTENDANCE_CLOCK_IN_TIME##</p>
<p><strong>Clock-In IP:</strong> ##ATTENDANCE_CLOCK_IN_IP##</p>

<p>This entry has been recorded in the attendance system.</p>

HTML,
            'other_data' => []
        ],
        'company_employee_clock_out' => [
            'type' => "email_templates",
            'title' => "Employee Clock-Out Notification - ##EMPLOYEE_NAME##",
            'content' => <<<HTML
<p>Hello,</p>

<p>This is to inform you that <strong>##EMPLOYEE_NAME##</strong> has successfully <strong>clocked out</strong> for the day.</p>

<p><strong>Employee Id:</strong> ##EMPLOYEE_ID##</p>
<p><strong>Employee Name:</strong> ##EMPLOYEE_NAME##</p>
<p><strong>Clock-Out DateTime:</strong> ##ATTENDANCE_CLOCK_OUT_TIME##</p>
<p><strong>Clock-Out IP:</strong> ##ATTENDANCE_CLOCK_OUT_IP##</p>

<p>This entry has been recorded in the attendance system.</p>

HTML,
            'other_data' => []
        ],
        'company_employee_resignation_apply' => [
            'type' => "email_templates",
            'title' => "Resignation Submitted by ##EMPLOYEE_NAME##",
            'content' => <<<HTML
<p>Hello,</p>

<p>This is to inform you that <strong>##EMPLOYEE_NAME##</strong> has submitted a resignation request.</p>

<p><strong>Employee ID:</strong> ##EMPLOYEE_ID##</p>
<p><strong>Employee Name:</strong> ##EMPLOYEE_NAME##</p>
<p><strong>Designation:</strong> ##EMPLOYEE_DESIGNATION##</p>
<p><strong>Department:</strong> ##EMPLOYEE_DEPARTMENT##</p>
<p><strong>Resignation date:</strong> ##RESIGNATION_DATE##</p>
<p><strong>Reason for Resignation:</strong> ##RESIGNATION_REASON##</p>

<p>Please review the request and begin the necessary clearance and exit process.</p>

HTML,
            'other_data' => []
        ],
        'company_employee_complaint_apply' => [
            'type' => "email_templates",
            'title' => "New Complaint Submitted by ##EMPLOYEE_NAME##",
            'content' => <<<HTML
<p>Hello,</p>

<p>This is to inform you that <strong>##EMPLOYEE_NAME##</strong> (Employee ID: ##EMPLOYEE_ID##) has submitted a new complaint through the company system.</p>

<p><strong>Complaint Against:</strong> ##COMPLAINT_AGAINST##</p>
<p><strong>Complaint Subject:</strong> ##COMPLAINT_TITLE##</p>
<p><strong>Complaint Date:</strong> ##COMPLAINT_DATE##</p>

<p><strong>Details:</strong></p>
<p>##COMPLAINT_DESCRIPTION##</p>

<p>Please log in to the your accoujt and take appropriate action:</p>

<p>We recommend addressing this matter promptly and following internal complaint resolution procedures.</p>

HTML,
            'other_data' => []
        ],
        'company_employee_expense_apply' => [
            'type' => "email_templates",
            'title' => "New Expense Request Submitted by ##EMPLOYEE_NAME##",
            'content' => <<<HTML
<p>Hello,</p>

<p>This is to inform you that <strong>##EMPLOYEE_NAME##</strong> (Employee ID: ##EMPLOYEE_ID##) has submitted a new expense reimbursement request.</p>

<p><strong>Expense Category:</strong> ##EXPENSE_CATEGORY##</p>
<p><strong>Expense Amount:</strong> ##EXPENSE_AMOUNT##</p>
<p><strong>Expense Date:</strong> ##EXPENSE_DATE##</p>
<p><strong>Description:</strong> ##EXPENSE_DESCRIPTION##</p>

<p>You can review the full request and attached documents using by login to your account</p>

<p>Please process the request as per company reimbursement policy.</p>

HTML,
            'other_data' => []
        ],
        'company_employee_survey_submit' => [
            'type' => "email_templates",
            'title' => "Survey Feedback Submitted by ##EMPLOYEE_NAME##",
            'content' => <<<HTML
<p>Hello,</p>

<p>This is to inform you that <strong>##EMPLOYEE_NAME##</strong> (Employee ID: ##EMPLOYEE_ID##) has successfully submitted feedback for the <strong>##SURVEY_TITLE##</strong>.</p>

<p><strong>Survey Title:</strong> ##SURVEY_TITLE##</p>

<p>You may view the full feedback details and response data by login to your account.</p>

HTML,
            'other_data' => []
        ],
    ];


    public static function getNotificationArray($moduleName)
    {
        return self::$emailNotificaiotnArray;
    }

    public static function seedNotifications($companyId, $moduleName = '')
    {
        $notifications = self::getNotificationArray($moduleName);

        foreach ($notifications as $key => $notification) {
            $notificationCount = Settings::withoutGlobalScope(CompanyScope::class)
                ->where('setting_type', $notification['type'])
                ->where('name_key', $key)
                ->where('company_id', $companyId)
                ->count();

            if ($notificationCount == 0) {
                $newNotification = new Settings();
                $newNotification->company_id = $companyId;
                $newNotification->setting_type = $notification['type'];
                $newNotification->name = $notification['title'];
                $newNotification->name_key = $key;
                $newNotification->credentials = [
                    'title' => $notification['title'],
                    'content' => $notification['content'],
                ];
                $newNotification->save();
            }
        }
    }

    public static function seedAllModulesNotifications($companyId)
    {
        // Main Module
        self::seedNotifications($companyId);
    }
}
