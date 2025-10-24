<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->date('date')->nullable()->default(null);
            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_leave')->default(false);
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('leave_id')->unsigned()->nullable()->default(null);
            $table->foreign('leave_id')->references('id')->on('leaves')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('leave_type_id')->unsigned()->nullable()->default(null);
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('holiday_id')->unsigned()->nullable()->default(null);
            $table->foreign('holiday_id')->references('id')->on('holidays')->onDelete('cascade')->onUpdate('cascade');
            $table->dateTime('clock_in_date_time')->nullable()->default(null);
            $table->dateTime('clock_out_date_time')->nullable()->default(null);
            $table->string('clock_in_ip_address')->nullable()->default(null);
            $table->integer('total_duration')->nullable()->default(null);
            $table->string('clock_out_ip_address')->nullable()->default(null);
            $table->time('clock_in_time')->nullable()->default(null);
            $table->time('clock_out_time')->nullable()->default(null);
            $table->time('office_clock_in_time')->nullable()->default(null);
            $table->time('office_clock_out_time')->nullable()->default(null);
            $table->boolean('is_half_day')->default(false);
            $table->boolean('is_late')->default(false);
            $table->boolean('is_paid')->default(0);
            $table->string('status')->default('present');
            $table->text('reason')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
