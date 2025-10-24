<?php

namespace Database\Seeders;

use App\Classes\CommonHrm;
use App\Models\Company;
use App\Models\Holiday;
use App\Models\User;
use App\Observers\HolidayObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('holidays')->delete();

        DB::statement('ALTER TABLE holidays AUTO_INCREMENT = 1');

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();

        $user = User::where('company_id', $company->id)->where('status', 'active')->first();

        // Manually Dispatch Events
        Holiday::observe(HolidayObserver::class);

        $year = Carbon::now()->year;
        $allHolidays = [
            ['name' => 'New Year', 'year' => $year, 'month' => 1, 'date' => $year . '-01-01', 'created_by' => $user->id],
            ['name' => 'Makar Sankranti', 'year' => $year, 'month' => 1, 'date' => $year . '-01-14', 'created_by' => $user->id],
            ['name' => 'Maha Shivratri', 'year' => $year, 'month' => 2, 'date' => $year . '-02-26', 'created_by' => $user->id],
            ['name' => 'Holi', 'year' => $year, 'month' => 3, 'date' => $year . '-03-14', 'created_by' => $user->id],
            ['name' => 'Labour Day', 'year' => $year, 'month' => 5, 'date' => $year . '-05-01', 'created_by' => $user->id],
            ['name' => 'Raksha Bandhan', 'year' => $year, 'month' => 8, 'date' => $year . '-08-09', 'created_by' => $user->id],
            ['name' => 'Independence Day', 'year' => $year, 'month' => 8, 'date' => $year . '-08-15', 'created_by' => $user->id],
            ['name' => 'Ganesh Chaturthi', 'year' => $year, 'month' => 8, 'date' => $year . '-08-27', 'created_by' => $user->id],
            ['name' => 'Mahatma Gandhi / Dussehra', 'year' => $year, 'month' => 10, 'date' => $year . '-10-02', 'created_by' => $user->id],
            ['name' => 'Diwali', 'year' => $year, 'month' => 10, 'date' => $year . '-10-20', 'created_by' => $user->id],
            ['name' => 'Diwali', 'year' => $year, 'month' => 10, 'date' => $year . '-10-21', 'created_by' => $user->id],
            ['name' => 'Govardhan Puja', 'year' => $year, 'month' => 10, 'date' => $year . '-10-22', 'created_by' => $user->id],
            ['name' => 'Bhai Duj', 'year' => $year, 'month' => 10, 'date' => $year . '-10-23', 'created_by' => $user->id],
            ['name' => 'Christmas', 'year' => $year, 'month' => 12, 'date' => $year . '-12-25', 'created_by' => $user->id],
        ];

        foreach ($allHolidays as $allHoliday) {
            Holiday::create($allHoliday);
        }

        // Mark Weekend
        $startDate = $year . '-01-01';
        $endDate = $year . '-12-31';
        $ocassionName = 'Sunday';
        $markDayName = ['Sunday'];
        $isHalfDay = 0;
        $halfHoliday = 'before_break';

        CommonHrm::markWeekend($markDayName, $startDate, $endDate, $ocassionName, $isHalfDay, $halfHoliday);
    }
}
