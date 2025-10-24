<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\EmployeeFields;
use App\Models\FieldTypes;
use App\Observers\EmployeeFieldsObserver;
use App\Observers\FieldTypesObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('field_types')->delete();
        DB::table('employee_fields')->delete();

        DB::statement('ALTER TABLE field_types AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE employee_fields AUTO_INCREMENT = 1');

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();
        session(['company' => $company]);

        // Manually Dispatch Events
        FieldTypes::observe(FieldTypesObserver::class);
        EmployeeFields::observe(EmployeeFieldsObserver::class);

        $data = [
            [
                'field_type' => ['name' => 'Documents', 'visible_to_employee' => 1],
                'fields' => [
                    ['name' => 'Driving License Number', 'type' => 'text'],
                    ['name' => 'PAN Number', 'type' => 'text'],
                    ['name' => 'Aadhar Number/SSN', 'type' => 'text'],
                    ['name' => 'Voter ID Number', 'type' => 'text'],
                    ['name' => 'Driving License', 'type' => 'file'],
                    ['name' => 'Aadhar/SSN', 'type' => 'file'],
                    ['name' => 'PAN Card', 'type' => 'file'],
                    ['name' => 'Voter ID', 'type' => 'file'],
                ]
            ],
            [
                'field_type' => ['name' => 'Company Documents', 'visible_to_employee' => 1],
                'fields' => [
                    ['name' => 'Offer Letter', 'type' => 'text'],
                    ['name' => 'Joining Letter', 'type' => 'text'],
                    ['name' => 'Contract', 'type' => 'text'],
                    ['name' => 'Resume', 'type' => 'text'],
                    ['name' => 'Relieving letter', 'type' => 'text'],
                    ['name' => 'Experience Letter', 'type' => 'text'],
                ]
            ],
            [
                'field_type' => ['name' => 'Bank Details', 'visible_to_employee' => 1],
                'fields' => [
                    ['name' => 'Account Holder Name', 'type' => 'text'],
                    ['name' => 'Account Number', 'type' => 'text'],
                    ['name' => 'Bank Code', 'type' => 'text'],
                    ['name' => 'Bank Name', 'type' => 'text'],
                    ['name' => 'Branch Name', 'type' => 'text'],
                ]
            ],
            [
                'field_type' => ['name' => 'Graduation', 'visible_to_employee' => 1],
                'fields' => [
                    ['name' => 'School/College', 'type' => 'text'],
                    ['name' => 'Start Date', 'type' => 'text'],
                    ['name' => 'End Date', 'type' => 'text'],
                    ['name' => 'Details', 'type' => 'text'],
                ]
            ],
            [
                'field_type' => ['name' => 'Work Experience', 'visible_to_employee' => 1],
                'fields' => [
                    ['name' => 'Company Name', 'type' => 'text'],
                    ['name' => 'Job Title', 'type' => 'text'],
                    ['name' => 'From', 'type' => 'text'],
                    ['name' => 'To', 'type' => 'text'],
                    ['name' => 'Details', 'type' => 'text'],
                ]
            ],
            [
                'field_type' => ['name' => 'Immigration', 'visible_to_employee' => 1],
                'fields' => [
                    ['name' => 'Visa Type', 'type' => 'text'],
                    ['name' => 'Visa Number', 'type' => 'text'],
                    ['name' => 'Passport Number', 'type' => 'text'],
                    ['name' => 'Country', 'type' => 'text'],
                    ['name' => 'Expiry Date', 'type' => 'text'],
                ]
            ]
        ];

        foreach ($data as $entry) {
            $fieldType = FieldTypes::create([
                'company_id' => $company->id,
                'name' => $entry['field_type']['name'],
                'visible_to_employee' => $entry['field_type']['visible_to_employee'],
            ]);


            foreach ($entry['fields'] as $field) {
                EmployeeFields::create([
                    'company_id' => $company->id,
                    'field_type_id' => $fieldType->id,
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'is_required' => 0,
                    'default_value' => null,
                ]);
            }
        }
    }
}
