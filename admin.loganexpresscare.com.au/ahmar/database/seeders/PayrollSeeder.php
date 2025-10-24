<?php

namespace Database\Seeders;

use App\Classes\Payrolls;
use App\Models\Company;
use App\Models\IncrementPromotion;
use App\Models\PrePayment;
use App\Models\SalaryGroupComponent;
use App\Models\StaffMember;
use App\Models\User;
use App\Observers\IncrementPromotionObserver;
use App\Observers\PrePaymentObserver;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('pre_payments')->delete();
        DB::table('increments_promotions')->delete();
        DB::table('salary_group_components')->delete();
        DB::table('salary_group_users')->delete();
        DB::table('salary_components')->delete();
        DB::table('salary_groups')->delete();
        DB::table('payrolls')->delete();
        DB::table('payroll_components')->delete();
        DB::table('basic_salary_details')->delete();

        DB::statement('ALTER TABLE pre_payments AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE increments_promotions AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE salary_group_components AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE salary_group_users AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE salary_components AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE salary_groups AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE payrolls AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE payroll_components AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE basic_salary_details AUTO_INCREMENT = 1');


        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();
        session(['company' => $company]);

        // Manually Dispatch Events
        PrePayment::observe(PrePaymentObserver::class);
        IncrementPromotion::observe(IncrementPromotionObserver::class);

        $allUsers = DB::table('users')->where('company_id', $company->id)
            ->where('status', 'active')->get()->toArray();
        $accounts = DB::table('accounts')->where('company_id', $company->id)->pluck('id')->toArray();
        $designations = DB::table('designations')->where('company_id', $company->id)->pluck('id')->toArray();

        $randomDates = $this->getRandomDates(45, fake()->numberBetween(15, 22));

        $incrementsRandomDates = $this->getRandomDates(45, fake()->numberBetween(15, 22));
        $incrementPromotionTypes = ['increment', 'decrement', 'promotion', 'increment_promotion', 'decrement_demotion'];

        foreach ($incrementsRandomDates as $incrementsRandomDate) {
            $user = fake()->randomElement($allUsers);
            $incrementType = fake()->randomElement($incrementPromotionTypes);

            $incrementPromotion = new IncrementPromotion();
            $incrementPromotion->company_id = $company->id;

            if ($incrementType == 'decrement' || $incrementType == 'decrement_demotion') {
                $newNetSalary = $user->net_salary ? $user->net_salary - fake()->randomElement([1000, 2000, 3000, 4000]) : fake()->randomElement([10000, 20000, 30000, 40000]);
                $incrementPromotion->net_salary = $newNetSalary;
            }
            if ($incrementType == 'increment' || $incrementType == 'increment_promotion') {
                $newNetSalary = $user->net_salary ? $user->net_salary + fake()->randomElement([1000, 2000, 3000, 4000]) : fake()->randomElement([10000, 20000, 30000, 40000]);
                $incrementPromotion->net_salary = $newNetSalary;
            }
            if ($incrementType == 'promotion' || $incrementType == 'increment_promotion' || $incrementType == 'decrement_demotion') {
                $incrementPromotion->current_designation_id = $user->designation_id;
                $incrementPromotion->promoted_designation_id = fake()->randomElement($designations);
            }

            $incrementPromotion->user_id = $user->id;
            $incrementPromotion->type = $incrementType;
            $incrementPromotion->date = $incrementsRandomDate;
            $incrementPromotion->description = fake()->sentence();
            $incrementPromotion->save();
        }

        if ($company) {
            $salaryGroupIds = $this->salaryGroups($company->id);
            $salaryComponentIds = $this->salaryComponents($company->id);
            $this->salaryGroupComponents($company->id, $salaryGroupIds, $salaryComponentIds);
            $this->salaryGroupUsers($company->id, $salaryGroupIds);
        }
    }

    function getRandomDates($beforeDays, $count = 5)
    {
        $dates = [];

        for ($i = 0; $i < $count; $i++) {
            $randomDate = Carbon::now()->subDays(rand(0, $beforeDays))->format('Y-m-d');
            $dates[] = $randomDate;
        }

        // Sort the dates in ascending order
        sort($dates);

        return $dates;
    }

    function getRandomDateTimeFromDate($date)
    {
        // Parse the given date and set a random time within the day
        return Carbon::parse($date)->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
    }

    function salaryGroups($companyId)
    {
        $groupNames = ['Group 1', 'Group 2', 'Group 3'];
        $salaryGroupIds = [];

        foreach ($groupNames as $groupName) {
            $salaryGroupIds[] = DB::table('salary_groups')->insertGetId([
                'company_id' => $companyId,
                'name' => $groupName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        return $salaryGroupIds;
    }

    function salaryComponents($companyId)
    {
        $components = [
            ['company_id' => $companyId, 'name' => 'House Rent Allowance', 'type' => 'earnings', 'value_type' => 'ctc_percent', 'monthly' => 20],
            ['company_id' => $companyId, 'name' => 'Medical Allowance', 'type' => 'earnings', 'value_type' => 'fixed', 'monthly' => 150],
            ['company_id' => $companyId, 'name' => 'Provident Fund', 'type' => 'deductions', 'value_type' => 'basic_percent', 'monthly' => 12],
            ['company_id' => $companyId, 'name' => 'Professional Tax', 'type' => 'deductions', 'value_type' => 'fixed', 'monthly' => 200],
            ['company_id' => $companyId, 'name' => 'Conveyance Allowance', 'type' => 'earnings', 'value_type' => 'fixed', 'monthly' => 120],
            ['company_id' => $companyId, 'name' => 'Bonus', 'type' => 'earnings', 'value_type' => 'variable', 'monthly' => 500],
            ['company_id' => $companyId, 'name' => 'Income Tax', 'type' => 'deductions', 'value_type' => 'ctc_percent', 'monthly' => 10],
        ];

        $salaryComponentIds = [];

        foreach ($components as $component) {
            $component['created_at'] = Carbon::now();
            $component['updated_at'] = Carbon::now();
            $salaryComponentIds[] = DB::table('salary_components')->insertGetId($component);
        }

        return $salaryComponentIds;
    }

    function salaryGroupComponents($companyId, $salaryGroupIds, $salaryComponentIds)
    {
        $salaryGroupComponents = [];

        foreach ($salaryGroupIds as $groupId) {
            $randomComponents = collect($salaryComponentIds)->random(rand(3, 5))->toArray();

            foreach ($randomComponents as $componentId) {
                $salaryGroupComponents[] = [
                    'company_id' => $companyId,
                    'salary_group_id' => $groupId,
                    'salary_component_id' => $componentId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        DB::table('salary_group_components')->insert($salaryGroupComponents);
    }


    function salaryGroupUsers($companyId, $salaryGroupIds)
    {
        $users = StaffMember::where('company_id', $companyId)
            ->pluck('id')->shuffle()
            ->toArray();

        $totalGroups = count($salaryGroupIds);

        $salaryGroupUsers = [];
        $basicSalaryDetails = [];
        $userAnnualCTC = [];

        foreach ($users as $index => $userId) {
            $groupId = $salaryGroupIds[$index % $totalGroups];

            $annualCTCOptions = range(12000, 40000, 4000);
            $annualCTC = collect($annualCTCOptions)->random();

            $ctcValueOptions = range(10, 50, 10);
            $ctcValue = collect($ctcValueOptions)->random();

            // Assign user to salary group
            $salaryGroupUsers[] = [
                'company_id' => $companyId,
                'salary_group_id' => $groupId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ];

            StaffMember::where('id', $userId)->update([
                'salary_group_id' => $groupId,
                'annual_ctc' => $annualCTC,
                'ctc_value' => $ctcValue,
                'updated_at' => now()
            ]);

            // Store user salary details for later update
            $userAnnualCTC[$userId] = $annualCTC;

            // Get salary group components for this group
            $salaryGroupComponents = SalaryGroupComponent::where('salary_group_id', $groupId)
                ->with('salaryComponent')
                ->get();

            foreach ($salaryGroupComponents as $component) {
                $salaryComponent = $component->salaryComponent;

                $basicSalaryDetails[] = [
                    'user_id' => $userId,
                    'salary_component_id' => $salaryComponent->id,
                    'type' => $salaryComponent->type,
                    'value_type' => $salaryComponent->value_type,
                    'monthly' => $salaryComponent->monthly,
                    'company_id' => $companyId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        // Insert salary group assignments
        DB::table('salary_group_users')->insert($salaryGroupUsers);

        // Insert salary components into basicSalaryDetails
        if (!empty($basicSalaryDetails)) {
            DB::table('basic_salary_details')->insert($basicSalaryDetails);
        }

        // Now update user salary after inserting basic salary details
        foreach ($userAnnualCTC as $userId => $annualCTC) {
            Payrolls::updateUserSalary($userId, $annualCTC);
        }

        // Generate Payroll for the last 3 months
        for ($i = 0; $i < 3; $i++) {
            $month = now()->subMonths($i)->month;
            $year = now()->subMonths($i)->year;

            $payrollGenerateRequest = new Request([
                'year' => $year,
                'month' => $month,
            ]);

            $company = company();
            $user = User::where('company_id', $company->id)
                ->where('name', 'Admin')
                ->where('status', 'active')
                ->first();

            Payrolls::payrollGenerateRegenerate($payrollGenerateRequest, $user, $company);
        }
    }
}
