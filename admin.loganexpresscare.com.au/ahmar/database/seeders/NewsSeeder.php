<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\News;
use App\Observers\NewsObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('news')->delete();

        DB::statement('ALTER TABLE news AUTO_INCREMENT = 1');

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();
        session(['company' => $company]);

        // Manually Dispatch Events
        News::observe(NewsObserver::class);

        DB::table('news')->insert([
            ['company_id' => $company->id, 'title' => 'Company Achieves Milestone', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Our company reached a new milestone in revenue growth.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'New Office Opening', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'We are pleased to announce our new office location.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Product Launch', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Our latest product is now available for customers.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Partnership Announcement', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'We have partnered with XYZ for future projects.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Employee of the Month', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Congratulations to our employee of the month!', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Annual Financial Report Released', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Our annual financial report is now available.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'New Investment Opportunities', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Explore our latest investment plans.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Customer Success Story', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'A success story from our valued customer.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'IT System Maintenance', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Scheduled maintenance on our IT systems.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Upcoming Webinar', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Join us for an insightful webinar.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Holiday Announcement', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Office will be closed for the upcoming holiday.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'New CEO Appointment', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'We welcome our new CEO to the company.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Training Program Launched', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'A new training program for employees.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Charity Event', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Join us for our annual charity event.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Tech Upgrade', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'We are upgrading our IT infrastructure.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Sustainability Initiative', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Introducing our new green energy plan.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Work From Home Policy', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Updates to our remote work policy.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Customer Support Expansion', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Expanding our support team globally.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Employee Wellness Program', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Launching new wellness benefits.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Industry Awards Recognition', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'We have been recognized in industry awards.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Employee Training Initiative', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'New skill development courses available.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Cybersecurity Measures Update', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Strengthening our cybersecurity policies.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Customer Appreciation Week', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Special events for our loyal customers.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'New Product Features', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Exciting new features added to our products.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company->id, 'title' => 'Supply Chain Optimization', 'visible_to_all' => true, 'status' => fake()->randomElement(['draft', 'published']), 'description' => 'Enhancements in our supply chain management.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ]);
    }
}
