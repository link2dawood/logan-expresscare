<?php

namespace App\Classes;

use App\Models\Permission;
use Nwidart\Modules\Facades\Module;

class PermsSeed
{
    public static $mainPermissionsArray = [

        // Salary Component
        'salary_components_view' => [
            'name' => 'salary_components_view',
            'display_name' => 'Salary Component View'
        ],
        'salary_components_create' => [
            'name' => 'salary_components_create',
            'display_name' => 'Salary Component Create'
        ],
        'salary_components_edit' => [
            'name' => 'salary_components_edit',
            'display_name' => 'Salary Component Edit'
        ],
        'salary_components_delete' => [
            'name' => 'salary_components_delete',
            'display_name' => 'Salary Component Delete'
        ],

        // Company Expenses 
        'manage_company_expense' => [
            'name' => 'manage_company_expense',
            'display_name' => 'Manage Company Expenses'
        ],

        // Salary Group
        'salary_groups_view' => [
            'name' => 'salary_groups_view',
            'display_name' => 'Salary Group View'
        ],
        'salary_groups_create' => [
            'name' => 'salary_groups_create',
            'display_name' => 'Salary Group Create'
        ],
        'salary_groups_edit' => [
            'name' => 'salary_groups_edit',
            'display_name' => 'Salary Group Edit'
        ],
        'salary_groups_delete' => [
            'name' => 'salary_groups_delete',
            'display_name' => 'Salary Group Delete'
        ],

        // Users
        'users_view' => [
            'name' => 'users_view',
            'display_name' => 'Staff Member View'
        ],
        'users_create' => [
            'name' => 'users_create',
            'display_name' => 'Staff Member Create'
        ],
        'users_edit' => [
            'name' => 'users_edit',
            'display_name' => 'Staff Member Edit'
        ],
        'users_delete' => [
            'name' => 'users_delete',
            'display_name' => 'Staff Member Delete'
        ],

        // Asset
        'assets_view' => [
            'name' => 'assets_view',
            'display_name' => 'Asset View'
        ],
        'assets_create' => [
            'name' => 'assets_create',
            'display_name' => 'Asset Create'
        ],
        'assets_edit' => [
            'name' => 'assets_edit',
            'display_name' => 'Asset Edit'
        ],
        'assets_delete' => [
            'name' => 'assets_delete',
            'display_name' => 'Asset Delete'
        ],

        // Asset Type
        'asset_types_view' => [
            'name' => 'asset_types_view',
            'display_name' => 'Asset Type View'
        ],
        'asset_types_create' => [
            'name' => 'asset_types_create',
            'display_name' => 'Asset Type Create'
        ],
        'asset_types_edit' => [
            'name' => 'asset_types_edit',
            'display_name' => 'Asset Type Edit'
        ],
        'asset_types_delete' => [
            'name' => 'asset_types_delete',
            'display_name' => 'Asset Type Delete'
        ],

        // Weekends & Holiday
        'holidays_view' => [
            'name' => 'holidays_view',
            'display_name' => 'Weekend/Holiday View'
        ],
        'holidays_create' => [
            'name' => 'holidays_create',
            'display_name' => 'Weekend/Holiday Create'
        ],
        'holidays_edit' => [
            'name' => 'holidays_edit',
            'display_name' => 'Weekend/Holiday Edit'
        ],
        'holidays_delete' => [
            'name' => 'holidays_delete',
            'display_name' => 'Weekend/Holiday Delete'
        ],

        // Leaves
        'leaves_view' => [
            'name' => 'leaves_view',
            'display_name' => 'Leaves View'
        ],
        'leaves_create' => [
            'name' => 'leaves_create',
            'display_name' => 'Leaves Create'
        ],
        'leaves_edit' => [
            'name' => 'leaves_edit',
            'display_name' => 'Leaves Edit'
        ],
        'leaves_delete' => [
            'name' => 'leaves_delete',
            'display_name' => 'Leaves Delete'
        ],

        'leaves_approve_reject' => [
            'name' => 'leaves_approve_reject',
            'display_name' => 'Leaves Approve/Reject'
        ],

        // work_durations
        'work_durations_view' => [
            'name' => 'work_durations_view',
            'display_name' => 'Work Durations View'
        ],
        'work_durations_create' => [
            'name' => 'work_durations_create',
            'display_name' => 'Work Durations Create'
        ],
        'work_durations_edit' => [
            'name' => 'work_durations_edit',
            'display_name' => 'Work Durations Edit'
        ],
        'work_durations_delete' => [
            'name' => 'work_durations_delete',
            'display_name' => 'Work Durations Delete'
        ],


        // Leave Type
        'leave_types_view' => [
            'name' => 'leave_types_view',
            'display_name' => 'Leave Types View'
        ],
        'leave_types_create' => [
            'name' => 'leave_types_create',
            'display_name' => 'Leave Types Create'
        ],
        'leave_types_edit' => [
            'name' => 'leave_types_edit',
            'display_name' => 'Leave Types Edit'
        ],
        'leave_types_delete' => [
            'name' => 'leave_types_delete',
            'display_name' => 'Leave Types Delete'
        ],

        // Attendance
        'attendances_view' => [
            'name' => 'attendances_view',
            'display_name' => 'Attendance View'
        ],
        'attendances_create' => [
            'name' => 'attendances_create',
            'display_name' => 'Attendance Create'
        ],
        'attendances_edit' => [
            'name' => 'attendances_edit',
            'display_name' => 'Attendance Edit'
        ],
        'attendances_delete' => [
            'name' => 'attendances_delete',
            'display_name' => 'Attendance Delete'
        ],

        // News
        'news_view' => [
            'name' => 'news_view',
            'display_name' => 'News View'
        ],
        'news_create' => [
            'name' => 'news_create',
            'display_name' => 'News Create'
        ],
        'news_edit' => [
            'name' => 'news_edit',
            'display_name' => 'News Edit'
        ],
        'news_delete' => [
            'name' => 'news_delete',
            'display_name' => 'News Delete'
        ],

        // Payroll
        'payrolls_view' => [
            'name' => 'payrolls_view',
            'display_name' => 'Payrolls View'
        ],
        'payrolls_create' => [
            'name' => 'payrolls_create',
            'display_name' => 'Payrolls Create'
        ],
        'payrolls_edit' => [
            'name' => 'payrolls_edit',
            'display_name' => 'Payrolls Edit'
        ],
        'payrolls_delete' => [
            'name' => 'payrolls_delete',
            'display_name' => 'Payrolls Delete'
        ],

        // Pre Payments
        'pre_payments_view' => [
            'name' => 'pre_payments_view',
            'display_name' => 'Pre Payments View'
        ],
        'pre_payments_create' => [
            'name' => 'pre_payments_create',
            'display_name' => 'Pre Payments Create'
        ],
        'pre_payments_edit' => [
            'name' => 'pre_payments_edit',
            'display_name' => 'Pre Payments Edit'
        ],
        'pre_payments_delete' => [
            'name' => 'pre_payments_delete',
            'display_name' => 'Pre Payments Delete'
        ],

        // Increment & Promotions
        'increments_promotions_view' => [
            'name' => 'increments_promotions_view',
            'display_name' => 'Increments Promotions View'
        ],
        'increments_promotions_create' => [
            'name' => 'increments_promotions_create',
            'display_name' => 'Increments Promotions Create'
        ],
        'increments_promotions_edit' => [
            'name' => 'increments_promotions_edit',
            'display_name' => 'Increments Promotions Edit'
        ],
        'increments_promotions_delete' => [
            'name' => 'increments_promotions_delete',
            'display_name' => 'Increments Promotions Delete'
        ],

        // Company Policy
        'company_policies_view' => [
            'name' => 'company_policies_view',
            'display_name' => 'Company Policy View'
        ],
        'company_policies_create' => [
            'name' => 'company_policies_create',
            'display_name' => 'Company Policy Create'
        ],
        'company_policies_edit' => [
            'name' => 'company_policies_edit',
            'display_name' => 'Company Policy Edit'
        ],
        'company_policies_delete' => [
            'name' => 'company_policies_delete',
            'display_name' => 'Company Policy Delete'
        ],

        // Warnings
        'warnings_view' => [
            'name' => 'warnings_view',
            'display_name' => 'Warnings View'
        ],
        'warnings_create' => [
            'name' => 'warnings_create',
            'display_name' => 'Warnings Create'
        ],
        'warnings_edit' => [
            'name' => 'warnings_edit',
            'display_name' => 'Warnings Edit'
        ],
        'warnings_delete' => [
            'name' => 'warnings_delete',
            'display_name' => 'Warnings Delete'
        ],

        // Resignations
        'resignations_view' => [
            'name' => 'resignations_view',
            'display_name' => 'Resignations View'
        ],
        'resignations_create' => [
            'name' => 'resignations_create',
            'display_name' => 'Resignations Create'
        ],
        'resignations_edit' => [
            'name' => 'resignations_edit',
            'display_name' => 'Resignations Edit'
        ],
        'resignations_delete' => [
            'name' => 'resignations_delete',
            'display_name' => 'Resignations Delete'
        ],

        // Termintions
        'terminations_view' => [
            'name' => 'terminations_view',
            'display_name' => 'Terminations View'
        ],
        'terminations_create' => [
            'name' => 'terminations_create',
            'display_name' => 'Terminations Create'
        ],
        'terminations_edit' => [
            'name' => 'terminations_edit',
            'display_name' => 'Terminations Edit'
        ],
        'terminations_delete' => [
            'name' => 'terminations_delete',
            'display_name' => 'Terminations Delete'
        ],

        // Indicators
        'indicators_view' => [
            'name' => 'indicators_view',
            'display_name' => 'Indicators View'
        ],
        'indicators_create' => [
            'name' => 'indicators_create',
            'display_name' => 'Indicators Create'
        ],
        'indicators_edit' => [
            'name' => 'indicators_edit',
            'display_name' => 'Indicators Edit'
        ],
        'indicators_delete' => [
            'name' => 'indicators_delete',
            'display_name' => 'Indicators Delete'
        ],

        // Appreciations
        'appreciations_view' => [
            'name' => 'appreciations_view',
            'display_name' => 'Appreciation View'
        ],
        'appreciations_create' => [
            'name' => 'appreciations_create',
            'display_name' => 'Appreciation Create'
        ],
        'appreciations_edit' => [
            'name' => 'appreciations_edit',
            'display_name' => 'Appreciation Edit'
        ],
        'appreciations_delete' => [
            'name' => 'appreciations_delete',
            'display_name' => 'Appreciation Delete'
        ],

        // Awards
        'awards_view' => [
            'name' => 'awards_view',
            'display_name' => 'Award View'
        ],
        'awards_create' => [
            'name' => 'awards_create',
            'display_name' => 'Award Create'
        ],
        'awards_edit' => [
            'name' => 'awards_edit',
            'display_name' => 'Award Edit'
        ],
        'awards_delete' => [
            'name' => 'awards_delete',
            'display_name' => 'Award Delete'
        ],

        //Salary Settings
        'salary_settings' => [
            'name' => 'salary_settings',
            'display_name' => 'Salary Settings'
        ],

        //Hrm Settings
        'hrm_settings' => [
            'name' => 'hrm_settings',
            'display_name' => 'Hrm Settings'
        ],


        //Font Settings
        'font_settings' => [
            'name' => 'font_settings',
            'display_name' => 'Font Settings'
        ],


        // Pdf Fonts
        'pdf_fonts_view' => [
            'name' => 'pdf_fonts_view',
            'display_name' => 'Pdf Fonts View'
        ],
        'pdf_fonts_create' => [
            'name' => 'pdf_fonts_create',
            'display_name' => 'Pdf Fonts Create'
        ],
        'pdf_fonts_edit' => [
            'name' => 'pdf_fonts_edit',
            'display_name' => 'Pdf Fonts Edit'
        ],
        'pdf_fonts_delete' => [
            'name' => 'pdf_fonts_delete',
            'display_name' => 'Pdf Fonts Delete'
        ],

        // Complaint
        'complaints_view' => [
            'name' => 'complaints_view',
            'display_name' => 'Complaint View'
        ],
        'complaints_create' => [
            'name' => 'complaints_create',
            'display_name' => 'Complaint Create'
        ],
        'complaints_edit' => [
            'name' => 'complaints_edit',
            'display_name' => 'Complaint Edit'
        ],
        'complaints_delete' => [
            'name' => 'complaints_delete',
            'display_name' => 'Complaint Delete'
        ],

        //Letter Head Templates
        'letter_head_templates_view' => [
            'name' => 'letter_head_templates_view',
            'display_name' => 'Letter Head Templates View'
        ],
        'letter_head_templates_create' => [
            'name' => 'letter_head_templates_create',
            'display_name' => 'Letter Head Templates Create'
        ],
        'letter_head_templates_edit' => [
            'name' => 'letter_head_templates_edit',
            'display_name' => 'Letter Head Templates Edit'
        ],
        'letter_head_templates_delete' => [
            'name' => 'letter_head_templates_delete',
            'display_name' => 'Letter Head Templates Delete'
        ],

        //generates
        'generates_view' => [
            'name' => 'generates_view',
            'display_name' => 'Generates View'
        ],
        'generates_create' => [
            'name' => 'generates_create',
            'display_name' => 'Generates Create'
        ],
        'generates_edit' => [
            'name' => 'generates_edit',
            'display_name' => 'Generates Edit'
        ],
        'generates_delete' => [
            'name' => 'generates_delete',
            'display_name' => 'Generates Delete'
        ],

        // Feedback
        'feedbacks_view' => [
            'name' => 'feedbacks_view',
            'display_name' => 'Feedback View'
        ],
        'feedbacks_create' => [
            'name' => 'feedbacks_create',
            'display_name' => 'Feedback Create'
        ],
        'feedbacks_edit' => [
            'name' => 'feedbacks_edit',
            'display_name' => 'Feedback Edit'
        ],
        'feedbacks_delete' => [
            'name' => 'feedbacks_delete',
            'display_name' => 'Feedback Delete'
        ],

        //Accounts
        'accounts_view' => [
            'name' => 'accounts_view',
            'display_name' => 'Account View'
        ],
        'accounts_create' => [
            'name' => 'accounts_create',
            'display_name' => 'Account Create'
        ],
        'accounts_edit' => [
            'name' => 'accounts_edit',
            'display_name' => 'Account Edit'
        ],
        'accounts_delete' => [
            'name' => 'accounts_delete',
            'display_name' => 'Account Delete'
        ],

        //Payee
        'payees_view' => [
            'name' => 'payees_view',
            'display_name' => 'Form View'
        ],
        'payees_create' => [
            'name' => 'payees_create',
            'display_name' => 'Form Create'
        ],
        'payees_edit' => [
            'name' => 'payees_edit',
            'display_name' => 'Form Edit'
        ],
        'payees_delete' => [
            'name' => 'payees_delete',
            'display_name' => 'Payee Delete'
        ],

        //Payers
        'payers_view' => [
            'name' => 'payers_view',
            'display_name' => 'Form View'
        ],
        'payers_create' => [
            'name' => 'payers_create',
            'display_name' => 'Form Create'
        ],
        'payers_edit' => [
            'name' => 'payers_edit',
            'display_name' => 'Form Edit'
        ],
        'payers_delete' => [
            'name' => 'payers_delete',
            'display_name' => 'Payers Delete'
        ],

        //Deposit Categories
        'deposit_categories_view' => [
            'name' => 'deposit_categories_view',
            'display_name' => 'Form View'
        ],
        'deposit_categories_create' => [
            'name' => 'deposit_categories_create',
            'display_name' => 'Form Create'
        ],
        'deposit_categories_edit' => [
            'name' => 'deposit_categories_edit',
            'display_name' => 'Form Edit'
        ],
        'deposit_categories_delete' => [
            'name' => 'deposit_categories_delete',
            'display_name' => 'Deposit Categories Delete'
        ],

        // Expense Categories
        'expense_categories_view' => [
            'name' => 'expense_categories_view',
            'display_name' => 'Expense Category View'
        ],
        'expense_categories_create' => [
            'name' => 'expense_categories_create',
            'display_name' => 'Expense Category Create'
        ],
        'expense_categories_edit' => [
            'name' => 'expense_categories_edit',
            'display_name' => 'Expense Category Edit'
        ],
        'expense_categories_delete' => [
            'name' => 'expense_categories_delete',
            'display_name' => 'Expense Category Delete'
        ],

        //Deposit
        'deposits_view' => [
            'name' => 'deposits_view',
            'display_name' => 'depoait View'
        ],
        'deposits_create' => [
            'name' => 'deposits_create',
            'display_name' => 'depoait Create'
        ],
        'deposits_edit' => [
            'name' => 'deposits_edit',
            'display_name' => 'depoait Edit'
        ],
        'deposits_delete' => [
            'name' => 'deposits_delete',
            'display_name' => 'Deposit Delete'
        ],

        // Employee Work Status
        'employee_work_status_view' => [
            'name' => 'employee_work_status_view',
            'display_name' => 'Employee Work Status View'
        ],
        'employee_work_status_create' => [
            'name' => 'employee_work_status_create',
            'display_name' => 'Employee Work Status Create'
        ],
        'employee_work_status_edit' => [
            'name' => 'employee_work_status_edit',
            'display_name' => 'Employee Work Status Edit'
        ],
        'employee_work_status_delete' => [
            'name' => 'employee_work_status_delete',
            'display_name' => 'Employee Work Status Delete'
        ],

        // Expense
        'expenses_view' => [
            'name' => 'expenses_view',
            'display_name' => 'Expense View'
        ],
        'expenses_create' => [
            'name' => 'expenses_create',
            'display_name' => 'Expense Create'
        ],
        'expenses_edit' => [
            'name' => 'expenses_edit',
            'display_name' => 'Expense Edit'
        ],
        'expenses_delete' => [
            'name' => 'expenses_delete',
            'display_name' => 'Expense Delete'
        ],
        'expenses_approve_reject' => [
            'name' => 'expenses_approve_reject',
            'display_name' => 'Expenses Approve/Reject'
        ],

        // Currency
        'currencies_view' => [
            'name' => 'currencies_view',
            'display_name' => 'Currency View'
        ],
        'currencies_create' => [
            'name' => 'currencies_create',
            'display_name' => 'Currency Create'
        ],
        'currencies_edit' => [
            'name' => 'currencies_edit',
            'display_name' => 'Currency Edit'
        ],
        'currencies_delete' => [
            'name' => 'currencies_delete',
            'display_name' => 'Currency Delete'
        ],

        // Location
        'locations_view' => [
            'name' => 'locations_view',
            'display_name' => 'Location View'
        ],
        'locations_create' => [
            'name' => 'locations_create',
            'display_name' => 'Location Create'
        ],
        'locations_edit' => [
            'name' => 'locations_edit',
            'display_name' => 'Location Edit'
        ],
        'locations_delete' => [
            'name' => 'locations_delete',
            'display_name' => 'Location Delete'
        ],

        // Form
        'forms_view' => [
            'name' => 'forms_view',
            'display_name' => 'Form View'
        ],
        'forms_create' => [
            'name' => 'forms_create',
            'display_name' => 'Form Create'
        ],
        'forms_edit' => [
            'name' => 'forms_edit',
            'display_name' => 'Form Edit'
        ],
        'forms_delete' => [
            'name' => 'forms_delete',
            'display_name' => 'Form Delete'
        ],

        // Modules
        'modules_view' => [
            'name' => 'modules_view',
            'display_name' => 'Modules View'
        ],

        // Role
        'roles_view' => [
            'name' => 'roles_view',
            'display_name' => 'Role View'
        ],
        'roles_create' => [
            'name' => 'roles_create',
            'display_name' => 'Role Create'
        ],
        'roles_edit' => [
            'name' => 'roles_edit',
            'display_name' => 'Role Edit'
        ],
        'roles_delete' => [
            'name' => 'roles_delete',
            'display_name' => 'Role Delete'
        ],

        // Company
        'companies_edit' => [
            'name' => 'companies_edit',
            'display_name' => 'Company Edit'
        ],

        // Translation
        'translations_view' => [
            'name' => 'translations_view',
            'display_name' => 'Translation View'
        ],
        'translations_create' => [
            'name' => 'translations_create',
            'display_name' => 'Translation Create'
        ],
        'translations_edit' => [
            'name' => 'translations_edit',
            'display_name' => 'Translation Edit'
        ],
        'translations_delete' => [
            'name' => 'translations_delete',
            'display_name' => 'Translation Delete'
        ],


        // Asset Lent/Return Settings
        'asset_lent_return_add_edit' => [
            'name' => 'asset_lent_return_add_edit',
            'display_name' => 'Asset Lent/Return Settings'
        ],

        // Mark Holiday Settings
        'mark_holidays_add_edit' => [
            'name' => 'mark_holidays_add_edit',
            'display_name' => 'Mark Holiday Settings'
        ],

        // Storage Settings
        'storage_edit' => [
            'name' => 'storage_edit',
            'display_name' => 'Storage Settings Edit'
        ],

        // Email Settings
        'email_edit' => [
            'name' => 'email_edit',
            'display_name' => 'Email Settings Edit'
        ],

        // Update App
        'update_app' => [
            'name' => 'update_app',
            'display_name' => 'Update App'
        ],

        // Departments
        'departments_view' => [
            'name' => 'departments_view',
            'display_name' => 'Department View'
        ],
        'departments_create' => [
            'name' => 'departments_create',
            'display_name' => 'Department Create'
        ],
        'departments_edit' => [
            'name' => 'departments_edit',
            'display_name' => 'Department Edit'
        ],
        'departments_delete' => [
            'name' => 'departments_delete',
            'display_name' => 'Department Delete'
        ],

        // Designations
        'designations_view' => [
            'name' => 'designations_view',
            'display_name' => 'Designation View'
        ],
        'designations_create' => [
            'name' => 'designations_create',
            'display_name' => 'Designation Create'
        ],
        'designations_edit' => [
            'name' => 'designations_edit',
            'display_name' => 'Designation Edit'
        ],
        'designations_delete' => [
            'name' => 'designations_delete',
            'display_name' => 'Designation Delete'
        ],
        // Shifts
        'shifts_view' => [
            'name' => 'shifts_view',
            'display_name' => 'Shift View'
        ],
        'shifts_create' => [
            'name' => 'shifts_create',
            'display_name' => 'Shift Create'
        ],
        'shifts_edit' => [
            'name' => 'shifts_edit',
            'display_name' => 'Shift Edit'
        ],
        'shifts_delete' => [
            'name' => 'shifts_delete',
            'display_name' => 'Shift Delete'
        ],


    ];

    public static $eStorePermissions = [];

    public static function getPermissionArray($moduleName = '')
    {
        return self::$mainPermissionsArray;
    }

    public static function seedPermissions($moduleName = '')
    {
        $permissions = self::getPermissionArray($moduleName);

        foreach ($permissions as $group => $permission) {
            $permissionCount = Permission::where('name', $permission['name'])->count();

            if ($permissionCount == 0) {
                $newPermission = new Permission();
                $newPermission->name = $permission['name'];
                $newPermission->display_name = $permission['display_name'];
                $newPermission->save();
            }
        }
    }

    public static function seedMainPermissions()
    {
        // Main Module
        self::seedPermissions();
    }

    public static function seedAllModulesPermissions()
    {
        // Main Module
        self::seedMainPermissions();

        $allModules = Module::all();
        foreach ($allModules as $allModule) {
            self::seedPermissions($allModule);
        }
    }
}
