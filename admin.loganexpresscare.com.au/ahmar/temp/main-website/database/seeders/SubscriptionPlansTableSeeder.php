<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        if (app_type() == 'saas') {
            Model::unguard();

            DB::table('subscription_plans')->delete();

            DB::statement('ALTER TABLE subscription_plans AUTO_INCREMENT = 1');

            \App\SuperAdmin\Classes\SuperAdminCommon::createSubscriptionPlans();
        }
    }
}
