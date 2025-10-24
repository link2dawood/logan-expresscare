<?php

namespace Database\Seeders;

use App\Classes\CommonHrm;
use App\Models\Appreciation;
use App\Models\Award;
use App\Models\Company;
use App\Models\User;
use App\Observers\AppreciationObserver;
use App\Observers\AwardObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AppreciationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('awards')->delete();
        DB::table('appreciations')->delete();

        DB::statement('ALTER TABLE awards AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE appreciations AUTO_INCREMENT = 1');

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();
        session(['company' => $company]);

        // Manually Dispatch Events
        Award::observe(AwardObserver::class);
        Appreciation::observe(AppreciationObserver::class);

        $admin = User::where('company_id', $company->id)->where('name', 'Admin')->where('status', 'active')->first();

        DB::table('awards')->insert([
            [
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'name' => 'Employee of the Month',
                'active' => true,
                'award_price' => 5000,
                'description' => 'Awarded to the best employee of the month.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'name' => 'Best Innovator Award',
                'active' => true,
                'award_price' => 10000,
                'description' => 'Recognizing the most innovative employee.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'name' => 'Team Excellence Award',
                'active' => false,
                'award_price' => 7500,
                'description' => 'Given to a team for outstanding performance.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'name' => 'Leadership Recognition',
                'active' => true,
                'award_price' => 12000,
                'description' => 'Award for exceptional leadership qualities.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'name' => 'Long Service Award',
                'active' => true,
                'award_price' => 15000,
                'description' => 'Awarded to employees with over 10 years of service.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $entries = fake()->numberBetween(17, 23);
        $users = DB::table('users')->where('company_id', $company->id)->pluck('id')->toArray();
        $accounts = DB::table('accounts')->where('company_id', $company->id)->pluck('id')->toArray();
        $firstAppreciationGiven = fake()->numberBetween(50, 60);

        for ($i = 1; $i <= $entries; $i++) {

            $randomAward = DB::table('awards')->where('company_id', $company->id)->inRandomOrder()->first();
            $awardGivenList = [
                ["price_given" => "Coffee Mug"],
                ["price_given" => "Movie Ticket"],
                ["price_given" => "Gift Voucher"],
                ["price_given" => "Personalized Pen"],
                ["price_given" => "Trophy"],
                ["price_given" => "Certificate of Excellence"],
                ["price_given" => "Lunch/Dinner Coupon"],
                ["price_given" => "Shopping Gift Card"],
                ["price_given" => "Books"],
                ["price_given" => "Bluetooth Speaker"],
                ["price_given" => "Fitness Band"],
                ["price_given" => "Customized T-Shirt"],
                ["price_given" => "Desk Plant"],
                ["price_given" => "Premium Notebook"],
                ["price_given" => "Wireless Earbuds"]
            ];;

            $appreciation = new Appreciation();
            $appreciation->company_id = $company->id;
            $appreciation->award_id = $randomAward->id;
            $appreciation->user_id = fake()->randomElement($users);
            $appreciation->account_id = fake()->randomElement($accounts);
            $appreciation->created_by = $admin->id;
            $appreciation->date = $firstAppreciationGiven > 0 ? Carbon::today()->setTime(rand(0, 23), rand(0, 59), rand(0, 59))->subDays($firstAppreciationGiven) : Carbon::today()->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
            $appreciation->price_amount = $randomAward->award_price;
            $appreciation->price_given = Arr::random($awardGivenList, fake()->numberBetween(1, 2));;
            $appreciation->save();

            $firstAppreciationGiven = $firstAppreciationGiven -  fake()->numberBetween(1, 3);

            CommonHrm::insertAccountEntries($appreciation->account_id, null, "appreciation", $appreciation->date, $appreciation->id, $appreciation->price_amount);
            CommonHrm::updateAccountAmount($appreciation->account_id);
        }
    }
}
