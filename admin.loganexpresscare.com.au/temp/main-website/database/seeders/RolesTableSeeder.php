<?php

namespace Database\Seeders;

use App\Classes\PermsSeed;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('permissions')->delete();
        DB::table('roles')->delete();

        DB::statement('ALTER TABLE `permissions` AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1');

        PermsSeed::seedPermissions();

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();

        $adminRole = new Role();
        $adminRole->company_id = $company->id;
        $adminRole->name = 'admin';
        $adminRole->display_name = 'Admin';
        $adminRole->description = 'Admin is allowed to manage everything of the app.';
        $adminRole->save();

        $allRoles = [
            [
                'name' => 'maintainer',
                'display_name' => 'Maintainer',
                'description' => 'Maintain all the stuff releated to company basic',
                'permissions' => [
                    'departments_view',
                    'departments_create',
                    'departments_edit',
                    'departments_delete',
                    'designations_view',
                    'designations_create',
                    'designations_edit',
                    'designations_delete',
                    'shifts_view',
                    'shifts_create',
                    'shifts_edit',
                    'shifts_delete',
                    'asset_types_view',
                    'asset_types_create',
                    'asset_types_edit',
                    'asset_types_delete',
                    'awards_view',
                    'awards_create',
                    'awards_edit',
                    'awards_delete',
                    'leave_types_view',
                    'leave_types_create',
                    'leave_types_edit',
                    'leave_types_delete',
                    'letter_head_templates_view',
                    'letter_head_templates_create',
                    'letter_head_templates_edit',
                    'letter_head_templates_delete',
                    'forms_view',
                    'forms_create',
                    'forms_edit',
                    'forms_delete',
                    'indicators_view',
                    'indicators_create',
                    'indicators_edit',
                    'indicators_delete',
                    'payee_view',
                    'payee_create',
                    'payee_edit',
                    'payee_delete',
                    'payers_view',
                    'payers_create',
                    'payers_edit',
                    'payers_delete',
                    'deposit_categories_view',
                    'deposit_categories_create',
                    'deposit_categories_edit',
                    'deposit_categories_delete',
                    'expense_categories_view',
                    'expense_categories_create',
                    'expense_categories_edit',
                    'expense_categories_delete',
                    'currencies_view',
                    'currencies_create',
                    'currencies_edit',
                    'currencies_delete',
                    'translations_view',
                    'translations_create',
                    'translations_edit',
                    'translations_delete',
                    'translations_view',
                    'translations_create',
                    'translations_edit',
                    'translations_delete',
                ]
            ],
            [
                'name' => 'supervisor',
                'display_name' => 'Supervisor',
                'description' => 'This role includes monitoring and guiding employees for their basic actions',
                'permissions' => [
                    'users_view',
                    'users_create',
                    'users_edit',
                    'users_delete',
                    'assets_view',
                    'asset_lent_return_add_edit',
                    'appreciations_view',
                    'appreciations_create',
                    'appreciations_edit',
                    'appreciations_delete',
                    'leaves_view',
                    'leaves_create',
                    'leaves_edit',
                    'leaves_delete',
                    'leaves_approve_reject',
                    'attendances_view',
                    'attendances_create',
                    'attendances_edit',
                    'attendances_delete',
                    'warnings_view',
                    'warnings_create',
                    'warnings_edit',
                    'warnings_delete',
                    'complaints_view',
                    'complaints_create',
                    'complaints_edit',
                    'complaints_delete',
                    'generatess_view',
                    'generatess_create',
                    'generatess_edit',
                    'generatess_delete',
                    'expenses_view',
                    'expenses_create',
                    'expenses_edit',
                    'expenses_delete',
                ]
            ],
            [
                'name' => 'hr',
                'display_name' => 'HR',
                'description' => 'Maintain all the stuff releated to team members',
                'permissions' => []
            ],
            [
                'name' => 'accountant',
                'display_name' => 'Accountant',
                'description' => 'Maintain all the stuff releated to billing and accounts',
                'permissions' => [
                    'pre_payments_view',
                    'pre_payments_create',
                    'pre_payments_edit',
                    'pre_payments_delete',
                    'pre_payments_view',
                    'pre_payments_create',
                    'pre_payments_edit',
                    'pre_payments_delete',
                    'increments_promotions_view',
                    'increments_promotions_create',
                    'increments_promotions_edit',
                    'increments_promotions_delete',
                    'payrolls_view',
                    'payrolls_create',
                    'payrolls_edit',
                    'payrolls_delete',
                    'salary_settings',
                    'salary_components_view',
                    'salary_components_create',
                    'salary_components_edit',
                    'salary_components_delete',
                    'salary_groups_view',
                    'salary_groups_create',
                    'salary_groups_edit',
                    'salary_groups_delete',
                    'awards_view',
                    'awards_create',
                    'awards_edit',
                    'awards_delete',
                    'appreciations_view',
                    'appreciations_create',
                    'appreciations_edit',
                    'appreciations_delete',
                    'accounts_view',
                    'accounts_create',
                    'accounts_edit',
                    'payee_delete',
                    'payee_view',
                    'payee_create',
                    'payee_edit',
                    'payee_delete',
                    'payers_view',
                    'payers_create',
                    'payers_edit',
                    'payers_delete',
                    'deposit_categories_view',
                    'deposit_categories_create',
                    'deposit_categories_edit',
                    'deposit_categories_delete',
                    'expense_categories_view',
                    'expense_categories_create',
                    'expense_categories_edit',
                    'expense_categories_delete',
                    'deposits_view',
                    'deposits_create',
                    'deposits_edit',
                    'deposits_delete',
                    'expenses_view',
                    'expenses_create',
                    'expenses_edit',
                    'expenses_delete',
                ]
            ]
        ];

        foreach ($allRoles as $allRole) {
            $newRole = new Role();
            $newRole->company_id = $company->id;
            $newRole->name = $allRole['name'];
            $newRole->display_name = $allRole['display_name'];;
            $newRole->description = $allRole['description'];;
            $newRole->save();

            if ($newRole->name == 'hr') {
                $allPermissionsArray = PermsSeed::$mainPermissionsArray;

                $allPermissionsKeys = array_keys($allPermissionsArray);
                $allPermissions = Permission::whereIn('name', $allPermissionsKeys)->pluck('id')->toArray();
            } else {
                $allPermissions = Permission::whereIn('name', $allRole['permissions'])->pluck('id')->toArray();
            }

            $newRole->savePermissions($allPermissions);
        }
    }
}
