<?php

namespace Database\Seeders;

use App\Classes\CommonHrm;
use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\Company;
use App\Models\Deposit;
use App\Observers\AccountEntryObserver;
use App\Observers\AccountObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepositeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('deposit_categories')->delete();
        DB::table('payers')->delete();
        DB::table('deposits')->delete();

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();

        DB::statement('ALTER TABLE deposit_categories AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE payers AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE deposits AUTO_INCREMENT = 1');

        // Manually Dispatch Events
        Account::observe(AccountObserver::class);
        AccountEntry::observe(AccountEntryObserver::class);

        $categories = [
            ['company_id' => $company->id, 'name' => 'Investment'],
            ['company_id' => $company->id, 'name' => 'Client Payment'],
            ['company_id' => $company->id, 'name' => 'Loan Disbursement'],
            ['company_id' => $company->id, 'name' => 'Vendor Refund'],
            ['company_id' => $company->id, 'name' => 'Government Subsidy'],
        ];
        DB::table('deposit_categories')->insert($categories);

        $payers = [];
        for ($i = 1; $i <= 3; $i++) {
            $payers[] = [
                'company_id' => $company->id,
                'name' => fake()->company(),
                'phone_number' => '91765432' . rand(10, 99),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        DB::table('payers')->insert($payers);

        // Seed expenses
        $accounts = DB::table('accounts')->where('company_id', $company->id)->pluck('id')->toArray();
        $allPayers = DB::table('payers')->where('company_id', $company->id)->pluck('id')->toArray();
        $allCategories = DB::table('deposit_categories')->where('company_id', $company->id)->pluck('id')->toArray();

        $allDeposites = [];
        for ($i = 0; $i < 60; $i++) {
            $allDeposites[] = [
                'company_id' => $company->id,
                'account_id' => fake()->randomElement($accounts),
                'deposit_category_id' => fake()->randomElement($allCategories),
                'amount' => rand(100, 5000),
                'date_time' => Carbon::now()->subDays(rand(1, 30)),
                'payer_id' => fake()->randomElement($allPayers),
                'reference_number' => strtoupper(uniqid('DEPO-')),
                'notes' => fake()->realText(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        foreach ($allDeposites as $allDeposite) {
            $deposit = Deposit::create($allDeposite);

            CommonHrm::insertAccountEntries($deposit->account_id, null, "deposit", $deposit->date_time, $deposit->id, $deposit->amount);
            CommonHrm::updateAccountAmount($deposit->account_id);
        }
    }
}
