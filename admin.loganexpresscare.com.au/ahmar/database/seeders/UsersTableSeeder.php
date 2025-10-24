<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeeWorkStatus;
use App\Models\Location;
use App\Models\Role;
use App\Models\Shift;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('locations')->delete();
        DB::table('departments')->delete();
        DB::table('designations')->delete();
        DB::table('users')->delete();
        DB::table('shifts')->delete();
        DB::table('role_user')->delete();
        DB::table('employee_work_status')->delete();

        DB::statement('ALTER TABLE locations AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE departments AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE designations AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE shifts AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE role_user AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE employee_work_status AUTO_INCREMENT = 1');

        $count = env('SEED_RECORD_COUNT', 30);

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();

        $allDesignations = [
            'HR Manager',
            'Recruitment Specialist',
            'HR Executive',
            'Training and Development Officer',
            'Payroll Administrator',
            'Chief Executive Officer',
            'Chief Financial Officer',
            'Finance Manager',
            'Accountant',
            'Financial Analyst',
            'Payroll Specialist',
            'Chief Technology Officer',
            'Software Engineer',
            'System Administrator',
            'IT Support Specialist',
            'Cybersecurity Analyst',
            'Sales Director',
            'Marketing Manager',
            'Sales Executive',
            'Digital Marketing Specialist',
            'Customer Relationship Manager',
            'Operations Manager',
            'Project Coordinator',
            'Supply Chain Analyst',
            'Logistics Manager',
            'Procurement Officer',
            'Customer Support Manager',
            'Customer Service Representative',
            'Technical Support Specialist',
            'Help Desk Associate',
            'Client Relations Executive',
            'Legal Advisor',
            'Corporate Lawyer',
            'Compliance Officer',
            'Paralegal',
            'Contracts Manager',
            'R&D Manager',
            'Product Designer',
            'Innovation Analyst',
            'Research Scientist',
            'Technical Writer',
            'Admin Manager',
            'Office Administrator',
            'Receptionist',
            'Facility Manager',
            'Clerical Assistant',
            'Production Manager',
            'Quality Control Specialist',
            'Manufacturing Engineer',
            'Assembly Line Supervisor',
            'Safety Officer'
        ];
        foreach ($allDesignations as $allDesignation) {
            $designation = new Designation();
            $designation->company_id = $company->id;
            $designation->name = $allDesignation;
            $designation->save();
        }

        // Designations
        $allDepartments = [
            'Human Resources',
            'Finance',
            'IT Department',
            'Sales & Marketing',
            'Operations',
            'Customer Support',
            'Legal',
            'Research & Development',
            'Administration',
            'Production'

        ];
        foreach ($allDepartments as $allDepartment) {
            $department = new Department();
            $department->company_id = $company->id;
            $department->name = $allDepartment;
            $department->save();
        }

        // Employee Work Status
        $employeeWorkStatus = [
            'Fulltime',
            'Contract',
            'Probation',
            'Work From Home',
        ];
        foreach ($employeeWorkStatus as $workStatus) {
            $employeeStatus = new EmployeeWorkStatus();
            $employeeStatus->company_id = $company->id;
            $employeeStatus->name = $workStatus;
            $employeeStatus->save();
        }

        $allShifts = [
            [
                'company_id' => $company->id,
                'name' => 'Morning Shift',
                'clock_in_time' => '09:30:00',
                'clock_out_time' => '18:00:00',
                'late_mark_after' => 30,
                'early_clock_in_time' => 10,
                'allow_clock_out_till' => 30,
                'self_clocking' => 1,
                'allowed_ip_address' => [],
                'is_next_day' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'company_id' => $company->id,
                'name' => 'Evening Shift',
                'clock_in_time' => '14:00:00',
                'clock_out_time' => '22:00:00',
                'late_mark_after' => 30,
                'early_clock_in_time' => 5,
                'allow_clock_out_till' => 20,
                'self_clocking' => 1,
                'allowed_ip_address' => [],
                'is_next_day' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'company_id' => $company->id,
                'name' => 'Night Shift',
                'clock_in_time' => '22:00:00',
                'clock_out_time' => '06:00:00',
                'late_mark_after' => 30,
                'early_clock_in_time' => 15,
                'allow_clock_out_till' => 40,
                'self_clocking' => 1,
                'allowed_ip_address' => [],
                'is_next_day' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($allShifts as $allShift) {
            Shift::create($allShift);
        }

        $adminDepartment = Department::where('company_id', $company->id)->where('name', 'IT Department')->first();
        $adminDesignation = Designation::where('company_id', $company->id)->where('name', 'Chief Executive Officer')->first();
        $shifts = DB::table('shifts')->where('company_id', $company->id)->pluck('id')->toArray();
        $departments = DB::table('departments')->where('company_id', $company->id)->pluck('id')->toArray();
        $designations = DB::table('designations')->where('company_id', $company->id)->pluck('id')->toArray();
        $employeeWorkStatus = DB::table('employee_work_status')->where('company_id', $company->id)->pluck('id')->toArray();

        $location = new Location();
        $location->company_id = $company->id;
        $location->name = 'Head Office';
        $location->save();

        // Admin User
        $admin = $this->generateUser($company, 'admin', $location, $shifts, $departments, $designations);
        $manager = $this->generateUser($company, 'manager', $location, $shifts, $departments, $designations, $admin->id);
        $departmentHead = $this->generateUser($company, 'department', $location, $shifts, $departments, $designations, $admin->id);
        $locationHead = $this->generateUser($company, 'location', $location, $shifts, $departments, $designations, $admin->id);
        $employee = $this->generateUser($company, 'employee', $location, $shifts, $departments, $designations, $admin->id);

        $company->admin_id = $admin->id;
        $company->save();

        // StaffMembers
        $allUsers = User::factory()->count((int)$count)->create([
            'company_id' => $company->id
        ]);

        // Manager User
        $allMangers = [$manager->id];

        $allUsers->each(function ($user) use ($company, $departments, $designations, $allMangers, $location, $shifts, $employeeWorkStatus) {
            $user->department_id = fake()->randomElement($departments);
            $user->designation_id = fake()->randomElement($designations);
            $user->employee_status_id = fake()->randomElement($employeeWorkStatus);
            $user->report_to = fake()->randomElement([0, 1]) == 1 ? fake()->randomElement($allMangers) : null;
            $user->location_id = $location->id;
            $user->company_id = $company->id;
            $user->dob = $this->generateDateBetweenYears(1980, 2005);
            $user->joining_date = $this->generateDateBetweenYears(2016, Carbon::now()->year - 1);
            $user->shift_id = fake()->randomElement($shifts);
            $user->save();
        });
    }

    public function generateDateBetweenYears($startYear, $endYear)
    {
        return Carbon::createFromTimestamp(mt_rand(
            Carbon::create($startYear, 1, 1)->timestamp,
            Carbon::create($endYear, 12, 31)->timestamp
        ));
    }

    public function generateUser($company, $type, $location, $shifts, $departments, $designations, $reportTo = null)
    {
        $data = [];
        $shifId = fake()->randomElement($shifts);

        if ($type == 'admin') {
            $adminRole = Role::where('company_id', $company->id)->where('name', 'admin')->first();
            $adminDepartment = Department::where('company_id', $company->id)->where('name', 'IT Department')->first();
            $adminDesignation = Designation::where('company_id', $company->id)->where('name', 'Chief Executive Officer')->first();
            $shift = Shift::where('company_id', $company->id)->where('name', 'Morning Shift')->first();
            $shifId = $shift->id;

            $data = [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'role_id' => $adminRole->id,
                'joining_date' => $this->generateDateBetweenYears(2015, 2015),
                'department_id' => $adminDepartment->id,
                'designation_id' => $adminDesignation->id,
                'visibility' => 'individual',
            ];
        } else {
            $role = Role::where('company_id', $company->id)->where('name', 'supervisor')->first();
            $name = $type == 'manager' || $type == 'employee' ? ucfirst($type) : ucfirst($type) . ' Head';

            $data = [
                'name' => $name,
                'email' => $type . '@example.com',
                'role_id' => $type == 'employee' ? null : $role->id,
                'joining_date' => $this->generateDateBetweenYears(2016, Carbon::now()->year - 1),
                'department_id' => fake()->randomElement($departments),
                'designation_id' => fake()->randomElement($designations),
                'visibility' => $type == 'employee' ? 'individual' : $type,
            ];
        }

        $user = new User();
        $user->company_id = $company->id;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = '12345678';
        $user->role_id = $data['role_id'];
        $user->user_type = "staff_members";
        $user->is_manager = $type == 'employee' ? 0 : 1;
        $user->department_id = $data['department_id'];
        $user->designation_id = $data['designation_id'];
        $user->location_id = $location->id;
        $user->visibility = $data['visibility'];
        $user->report_to = $reportTo;
        $user->shift_id = $shifId;
        $user->dob = $this->generateDateBetweenYears(1980, 2005);
        $user->joining_date = $data['joining_date'];
        $user->save();
        if ($type != 'employee') {
            $user->attachRole($data['role_id']);
        }

        return $user;
    }
}
