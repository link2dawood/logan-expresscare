<?php

use App\Classes\Common;
use App\Classes\NotificationSeed;
use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_global')->default(false);
            $table->string('setting_type');
            $table->string('name');
            $table->string('name_key');
            $table->text('credentials')->nullable()->default(null);
            $table->text('other_data')->nullable()->default(null);
            $table->boolean('status')->default(false);
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });

        if (app_type() == 'non-saas') {
            $company = Company::where('is_global', 0)->first();

            Common::insertInitSettings($company);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
