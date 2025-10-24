<?php

use App\Models\Currency;
use App\Models\Lang;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable()->default(NULL);
            $table->string('email')->nullable()->default(NULL);
            $table->string('phone')->nullable()->default(NULL);
            $table->string('website')->nullable()->default(NULL);
            $table->string('light_logo')->nullable()->default(NULL);
            $table->string('dark_logo')->nullable()->default(NULL);
            $table->string('small_dark_logo')->nullable()->default(NULL);
            $table->string('small_light_logo')->nullable()->default(NULL);
            $table->string('address', 1000)->nullable()->default(NULL);
            $table->string('app_layout', 10)->default('sidebar');
            $table->bigInteger('currency_id')->unsigned()->nullable();
            $table->bigInteger('lang_id')->unsigned()->nullable();
            $table->foreign('lang_id')->references('id')->on('langs')->onDelete('set null')->onUpdate('cascade');
            $table->string('left_sidebar_theme', 20)->default("dark");
            $table->string('primary_color', 20)->default("#1890ff");
            $table->string('date_format', 20)->default("DD-MM-YYYY");
            $table->string('time_format', 20)->default("hh:mm a");
            $table->boolean('auto_detect_timezone')->default(true); // Allow Browser To Auto Detect timezone For Logged In User
            $table->string('timezone')->default("Asia/Kolkata");
            $table->string('session_driver', 20)->default("file");
            $table->boolean('app_debug')->default(false);
            $table->boolean('update_app_notification')->default(true);
            $table->string('login_image')->nullable()->default(NULL);
            $table->boolean('rtl')->default(false);
            $table->string('shortcut_menus', 20)->default('top_bottom'); // top, bottom, top_bottom
            $table->string('mysqldump_command')->default("/usr/bin/mysqldump");
            $table->string('profile_image')->nullable()->default(null);

            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            $table->bigInteger('subscription_plan_id')->unsigned()->nullable()->default(null);
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('set null')->onUpdate('cascade');
            $table->enum('package_type', ['monthly', 'annual'])->default('monthly');
            $table->date('licence_expire_on')->nullable()->default(null);

            $table->boolean('is_global')->default(false);
            $table->bigInteger('admin_id')->unsigned()->nullable()->default(null);
            $table->string('status')->default('active');
            $table->integer('total_users')->default(1);
            $table->string('email_verification_code')->nullable()->default(null);
            $table->boolean('verified')->default(false);

            //fields from hrm modules
            $table->time('clock_in_time')->nullable()->default("09:30:00");
            $table->time('clock_out_time')->nullable()->default("18:00:00");
            $table->string('leave_start_month', 2)->default('01');
            $table->integer('late_mark_after')->nullable()->default(null);
            $table->integer('early_clock_in_time')->nullable()->default(null);
            $table->integer('allow_clock_out_till')->nullable()->default(null);
            $table->boolean('self_clocking')->default(1);
            $table->text('allowed_ip_address')->nullable()->default(null);

            //fields from letterhead pdf

            $table->string('letterhead_header_color', 20)->default("#1890ff");
            $table->boolean('letterhead_show_company_name')->default(true);
            $table->boolean('letterhead_show_company_email')->default(true);
            $table->boolean('letterhead_show_company_phone')->default(true);
            $table->boolean('letterhead_show_company_address')->default(true);

            $table->integer('letterhead_left_space')->nullable()->default(15);
            $table->integer('letterhead_right_space')->nullable()->default(15);
            $table->integer('letterhead_top_space')->nullable()->default(40);
            $table->integer('letterhead_bottom_space')->nullable()->default(20);
            $table->integer('holiday_pdf_font_size')->nullable()->default(20);
            $table->integer('holiday_pdf_line_height')->nullable()->default(40);
            $table->integer('generate_pdf_font_size')->nullable()->default(17);
            $table->integer('generate_pdf_line_height')->nullable()->default(28);
            $table->boolean('letterhead_title_show_in_pdf')->nullable()->default(true);

            // Employee id prefix

            $table->string('employee_id_prefix')->nullable()->default('emp');
            $table->string('employee_id_start')->nullable()->default('0001');
            $table->integer('employee_id_digits')->nullable()->default(4);


            $table->timestamps();
        });

        if (app_type() == 'non-saas') {
            $enLang = Lang::where('key', 'en')->first();

            // Creating entries using DB
            // So that no observer will be called
            DB::table('companies')->insert([
                'name' => 'Hrmifly',
                'short_name' => 'Hrmifly',
                'email' => 'company@example.com',
                'phone' => '+9199999999',
                'address' => '7 street, city, state, 762782',
                'lang_id' => $enLang->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
