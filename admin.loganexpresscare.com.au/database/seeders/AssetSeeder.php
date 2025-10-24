<?php

namespace Database\Seeders;

use App\Classes\CommonHrm;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssetUser;
use App\Models\Company;
use App\Models\User;
use App\Observers\AssetTypeObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('asset_types')->delete();
        DB::table('assets')->delete();
        DB::table('asset_users')->delete();

        DB::statement('ALTER TABLE asset_types AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE assets AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE asset_users AUTO_INCREMENT = 1');

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();
        session(['company' => $company]);

        // Manually Dispatch Events
        AssetType::observe(AssetTypeObserver::class);

        $assetTypes = [
            [
                'name' => 'Laptop',
                'assets' => [
                    ['name' => 'Dell Latitude 5520', 'description' => 'High-performance laptop for employees.', 'serial_number' => 'DL5520-12345', 'price' => 75000],
                    ['name' => 'MacBook Pro M2', 'description' => 'MacBook Pro for developers.', 'serial_number' => 'MBP-M2-9876', 'price' => 180000],
                ],
            ],
            [
                'name' => 'Desktop',
                'assets' => [
                    ['name' => 'HP EliteDesk 800', 'description' => 'Office desktop system.', 'serial_number' => 'HPED-4567', 'price' => 50000],
                    ['name' => 'Lenovo ThinkCentre M70t', 'description' => 'High-performance desktop.', 'serial_number' => 'LTC-M70t-5678', 'price' => 60000],
                ],
            ],
            [
                'name' => 'Mobile Phone',
                'assets' => [
                    ['name' => 'Samsung Galaxy S22', 'description' => 'Official mobile phone for managers.', 'serial_number' => 'SGS22-9876', 'price' => 65000],
                    ['name' => 'iPhone 14 Pro', 'description' => 'Business executive phone.', 'serial_number' => 'IP14-4567', 'price' => 130000],
                ],
            ],
            [
                'name' => 'Tablet',
                'assets' => [
                    ['name' => 'Apple iPad Air', 'description' => 'For field employees.', 'serial_number' => 'IPAD-3345', 'price' => 85000],
                    ['name' => 'Samsung Galaxy Tab S8', 'description' => 'Used for design reviews.', 'serial_number' => 'SGT-S8-2244', 'price' => 75000],
                ],
            ],
            [
                'name' => 'Furniture',
                'assets' => [
                    ['name' => 'Office Chair - Ergonomic', 'description' => 'Comfortable ergonomic chair.', 'serial_number' => null, 'price' => 5000],
                    ['name' => 'Standing Desk', 'description' => 'Height adjustable desk.', 'serial_number' => null, 'price' => 12000],
                ],
            ],
            [
                'name' => 'Vehicle',
                'assets' => [
                    ['name' => 'Toyota Innova Car', 'description' => 'Company vehicle for business travel.', 'serial_number' => 'TN-5678', 'price' => 1500000],
                    ['name' => 'Honda Activa Scooter', 'description' => 'For delivery and office errands.', 'serial_number' => 'ACTIVA-7890', 'price' => 85000],
                ],
            ],
            [
                'name' => 'Software License',
                'assets' => [
                    ['name' => 'Adobe Creative Cloud', 'description' => 'Subscription for designers.', 'serial_number' => 'ACC-8765', 'price' => 35000],
                    ['name' => 'Microsoft Office 365', 'description' => 'Office productivity suite.', 'serial_number' => 'MSO365-1234', 'price' => 15000],
                ],
            ],
            [
                'name' => 'Printer',
                'assets' => [
                    ['name' => 'HP LaserJet Pro', 'description' => 'Office printer for official documents.', 'serial_number' => 'HPLJ-9090', 'price' => 25000],
                    ['name' => 'Epson EcoTank L3150', 'description' => 'Ink tank printer for bulk printing.', 'serial_number' => 'ETL3150-7745', 'price' => 18000],
                ],
            ],
            [
                'name' => 'Projector',
                'assets' => [
                    ['name' => 'BenQ MW560', 'description' => 'Conference room projector.', 'serial_number' => 'BQMW560-9911', 'price' => 45000],
                    ['name' => 'Epson EB-E01', 'description' => 'Portable projector for presentations.', 'serial_number' => 'EPEB-E01-2345', 'price' => 38000],
                ],
            ],
            [
                'name' => 'Other',
                'assets' => [
                    ['name' => 'External Hard Drive', 'description' => '1TB external storage.', 'serial_number' => 'EHD-1122', 'price' => 8000],
                    ['name' => 'Noise Cancelling Headphones', 'description' => 'Wireless headphones for remote work.', 'serial_number' => 'NCH-4455', 'price' => 12000],
                ],
            ],
        ];



        $location = DB::table('locations')->where('company_id', $company->id)->first('id');
        $accounts = DB::table('accounts')->where('company_id', $company->id)->pluck('id')->toArray();

        $admin = User::where('company_id', $company->id)->where('name', 'Admin')->where('status', 'active')->first();
        $allRoleUsers = DB::table('users')->where('company_id', $company->id)
            ->whereNotNull('role_id')
            ->where('status', 'active')->pluck('id')->toArray();

        // Insert data using Eloquent Model
        foreach ($assetTypes as $assetType) {
            $type = new AssetType();
            $type->company_id = $company->id;
            $type->name = $assetType['name'];
            $type->save();

            $allAssets = $assetType['assets'];

            foreach ($allAssets as $allAsset) {
                $workingStatus = fake()->randomElement(['working', 'not_working']);

                $purchaseDayBefore = fake()->numberBetween(20, 60);
                $isBreakEntry = fake()->randomElement([0, 0, 1]) && $workingStatus == 'not_working';

                $isOnlyLent = fake()->randomElement([0, 0, 1]) && $workingStatus == 'working';

                $asset = new Asset();
                $asset->company_id = $company->id;
                $asset->asset_type_id = $type->id;
                $asset->name = $allAsset['name'];
                $asset->serial_number = $allAsset['serial_number'];
                $asset->description = $allAsset['description'];
                $asset->location_id = $location->id;
                $asset->status = $workingStatus;
                $asset->account_id = fake()->randomElement($accounts);
                $asset->price = $allAsset['price'];
                $asset->purchase_date = Carbon::now()->subDays($purchaseDayBefore);

                $asset->save();

                CommonHrm::insertAccountEntries($asset->account_id, null, "asset", $asset->purchase_date, $asset->id, $asset->price);
                CommonHrm::updateAccountAmount($asset->account_id);

                $assetEntries = fake()->numberBetween(3, 5);

                $lentDay = fake()->numberBetween(15, 19);

                for ($i = 1; $i <= $assetEntries; $i++) {

                    $lentTo = fake()->randomElement($allRoleUsers);

                    $assetUser = new AssetUser();
                    $assetUser->asset_id = $asset->id;
                    $assetUser->lent_to = $lentTo;
                    $assetUser->lent_by = $admin->id;
                    $assetUser->lend_date = Carbon::now()->subDays($lentDay);

                    $lentDay = $lentDay - fake()->numberBetween(1, 3);

                    $assetUser->return_date = Carbon::now()->subDays($lentDay);
                    $assetUser->return_by = $i == $assetEntries && $isOnlyLent ? null : $admin->id;
                    $assetUser->broken_user_id = $i == $assetEntries && $isBreakEntry ? $lentTo : null;
                    $assetUser->actual_return_date = $isOnlyLent ? null : Carbon::now()->subDays($lentDay);
                    $assetUser->save();

                    if ($i == $assetEntries && ($isBreakEntry || $isOnlyLent)) {
                        $asset->broken_by = $isBreakEntry ? $lentTo : null;
                        $asset->user_id = $isOnlyLent ? $lentTo : null;
                        $asset->asset_user_id = $isOnlyLent ? $assetUser->id : null;
                        $asset->save();

                        break;
                    }
                }
            }
        }
    }
}
