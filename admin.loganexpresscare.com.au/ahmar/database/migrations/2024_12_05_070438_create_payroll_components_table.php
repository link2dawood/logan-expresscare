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
        Schema::create('payroll_components', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('payroll_id')->unsigned()->nullable()->default(null);
            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('pre_payment_id')->unsigned()->nullable()->default(null);
            $table->foreign('pre_payment_id')->references('id')->on('pre_payments')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('expense_id')->unsigned()->nullable()->default(null);
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('salary_component_id')->unsigned()->nullable()->default(null);
            $table->foreign('salary_component_id')->references('id')->on('salary_components')->onDelete('set null')->onUpdate('cascade');
            $table->string('name');
            $table->string('value_type')->default('fixed');
            $table->double('amount');
            $table->double('monthly_value')->nullable()->default(null);
            $table->boolean('is_earning')->default(1);
            $table->string('type', 20)->default('pre_payments'); // ['pre_payments', 'customs', 'expenses]
            $table->string('flags', 50)->nullable()->default(null);;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_components');
    }
};
