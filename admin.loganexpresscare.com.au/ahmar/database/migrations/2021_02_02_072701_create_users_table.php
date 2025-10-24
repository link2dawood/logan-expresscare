<?php

use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('role_id')->unsigned()->nullable()->default(null);
            $table->boolean('is_superadmin')->default(false);
            $table->string('user_type')->default('staff_members');
            $table->boolean('login_enabled')->default(true);
            $table->string('name');
            $table->string('employee_number')->nullable()->default(null);
            $table->string('gender', 20)->default('female');
            $table->date('dob')->nullable()->default(null);
            $table->date('joining_date')->nullable()->default(null);
            $table->boolean('allow_login')->default(true);
            $table->string('email');
            $table->boolean('is_married')->nullable()->default(false);
            $table->date('marriage_date')->nullable()->default(null);
            $table->string('personal_email')->nullable()->default(null);
            $table->string('personal_phone')->nullable()->default(null);
            $table->bigInteger('report_to')->unsigned()->nullable()->default(null);
            $table->foreign('report_to')->references('id')->on('users')->onDelete('SET null');
            $table->string('is_manager')->nullable()->default(false);
            $table->string('visibility', 20)->default('individual');
            $table->string('password')->nullable();
            $table->string('phone')->nullable()->default(null);
            $table->date('end_date')->nullable()->default(null);
            $table->date('probation_start_date')->nullable()->default(null);
            $table->date('probation_end_date')->nullable()->default(null);
            $table->date('notice_start_date')->nullable()->default(null);
            $table->date('notice_end_date')->nullable()->default(null);
            $table->string('profile_image')->nullable()->default(null);
            $table->string('address', 1000)->nullable()->default(null);
            $table->string('shipping_address', 1000)->nullable()->default(null);
            $table->string('email_verification_code', 50)->nullable()->default(null);

            $table->string('status')->default('active');
            $table->string('reset_code')->nullable()->default(null);
            $table->string('timezone', 50)->default('Asia/Kolkata');
            $table->string('date_format', 20)->default('d-m-Y');
            $table->string('date_picker_format', 20)->default('dd-mm-yyyy');
            $table->string('time_format', 20)->default('h:i a');
            $table->string('net_salary')->nullable()->default(null);
            $table->double('basic_salary')->nullable()->default(null);
            $table->bigInteger('location_id')->unsigned()->nullable()->default(null);
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('salary_group_id')->unsigned()->nullable()->default(null);
            $table->foreign('salary_group_id')->references('id')->on('salary_groups')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('employee_status_id')->unsigned()->nullable()->default(null);
            $table->foreign('employee_status_id')->references('id')->on('employee_work_status')->onDelete('cascade')->onUpdate('cascade');


            $table->timestamps();
        });

        if (app_type() == 'non-saas') {
            $company = Company::where('is_global', 0)->first();

            $adminId = DB::table('users')->insertGetId([
                'company_id' => $company->id,
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('12345678'),
                'user_type' => 'staff_members',
                'is_manager' => 1
            ]);

            $company->admin_id = $adminId;
            $company->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
