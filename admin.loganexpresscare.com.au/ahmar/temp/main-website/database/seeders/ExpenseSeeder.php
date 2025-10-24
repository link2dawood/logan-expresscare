<?php

namespace Database\Seeders;

use App\Classes\CommonHrm;
use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\Company;
use App\Models\Expense;
use App\Observers\AccountEntryObserver;
use App\Observers\AccountObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('expense_categories')->delete();
        DB::table('accounts')->delete();
        DB::table('payees')->delete();
        DB::table('expenses')->delete();
        DB::table('account_entries')->delete();

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();

        DB::statement('ALTER TABLE expense_categories AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE accounts AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE payees AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE expenses AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE account_entries AUTO_INCREMENT = 1');

        // Manually Dispatch Events
        Account::observe(AccountObserver::class);
        AccountEntry::observe(AccountEntryObserver::class);

        $categories = [
            ['company_id' => $company->id, 'expense_for' => 'employee_specific', 'name' => 'Office Supplies', 'description' => 'Expenses related to office supplies.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'expense_for' => 'company_specific', 'name' => 'Travel', 'description' => 'Travel and transportation expenses.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'expense_for' => 'company_specific', 'name' => 'Utilities', 'description' => 'Electricity, water, and internet bills.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'expense_for' => 'all', 'name' => 'Miscellaneous', 'description' => 'Other unclassified expenses.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];


        DB::table('expense_categories')->insert($categories);

        $allAccounts = [];
        $bankNames = ['Standard Chartered Bank', 'JP Morgan Chase Bank', 'ICICI Bank'];
        foreach ($bankNames as $bankName) {
            $allAccounts[] = [
                'company_id' => $company->id,
                'name' => $bankName,
                'initial_balance' => fake()->randomElement([1000, 3000, 2000]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        foreach ($allAccounts as $allAccount) {
            $account = Account::create($allAccount);
            $today = Carbon::now();

            CommonHrm::insertAccountEntries($account->id, null, "initial_balance", $today, $account->id, $account->initial_balance);
            CommonHrm::updateAccountAmount($account->id);
        }

        $payees = [];
        for ($i = 1; $i <= 3; $i++) {
            $payees[] = [
                'company_id' => $company->id,
                'name' => fake()->name(),
                'phone_number' => '98765432' . rand(10, 99),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        DB::table('payees')->insert($payees);

        // Seed expenses
        $expenseTypes = ['employee_claims', 'company_claims'];
        $users = DB::table('users')->where('company_id', $company->id)->where('status', 'active')->pluck('id')->toArray();
        $accounts = DB::table('accounts')->where('company_id', $company->id)->pluck('id')->toArray();
        $payees = DB::table('payees')->where('company_id', $company->id)->pluck('id')->toArray();
        $categories = DB::table('expense_categories')->where('company_id', $company->id)->pluck('id')->toArray();

        $allExpenses = [];
        for ($i = 0; $i < 40; $i++) {
            $expenseType = fake()->randomElement($expenseTypes);
            $paymentStatus = $expenseType == 'company_claims' ? 1 : fake()->randomElement([0, 1]); // 1 => Right Now, 0 => Deduct From Payroll
            $expenseStatus = $expenseType == 'company_claims' ? 'approved' : fake()->randomElement(['pending', 'approved', 'rejected']);

            $dateTime = Carbon::now()->subDays(rand(1, 30));

            $allExpenses[] = [
                'company_id' => $company->id,
                'bill' => null,
                'expense_category_id' => fake()->randomElement($categories),
                'amount' => rand(100, 5000),
                'user_id' => $expenseType == 'employee_claims' ? fake()->randomElement($users) : null,
                'notes' => fake()->realText(),
                'date_time' => $dateTime,
                'reference_number' => strtoupper(uniqid('EXP-')),
                'status' => $expenseStatus,
                'expense_type' => $expenseType,

                'payment_status' => $paymentStatus,
                'account_id' => $paymentStatus ? fake()->randomElement($accounts) : null,
                'payment_date' => $paymentStatus ? Carbon::now() : null,
                'payee_id' => $expenseType == 'company_claims' ? fake()->randomElement($payees) : null,
                'payroll_month' => $paymentStatus ? null : $dateTime->copy()->month,
                'payroll_year' => $paymentStatus ? null : $dateTime->copy()->year,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }



        foreach ($allExpenses as $allExpense) {
            $expense = Expense::create($allExpense);

            if ($expense->payment_status == 1 && $expense->status == 'approved') {

                CommonHrm::insertAccountEntries($expense->account_id, null, "expense", $expense->payment_date, $expense->id, $expense->amount);
                CommonHrm::updateAccountAmount($expense->account_id);
            }
        }
    }
}
