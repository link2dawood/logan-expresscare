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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->double('basic_salary');
            $table->double('salary_amount');
            $table->double('pre_payment_amount')->default(0);
            $table->double('expense_amount')->default(0);
            $table->double('net_salary');
            $table->float('total_days');
            $table->float('working_days');
            $table->float('present_days');
            $table->integer('total_office_time');
            $table->integer('total_worked_time');
            $table->integer('half_days');
            $table->float('late_days');
            $table->float('paid_leaves');
            $table->float('unpaid_leaves');
            $table->float('holiday_count');
            $table->date('payment_date')->nullable()->default(null);
            $table->string('status')->default('generated');
            $table->bigInteger('created_by')->unsigned()->nullable()->default(null);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('updated_by')->unsigned()->nullable()->default(null);
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('account_id')->unsigned()->nullable()->default(null);
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};