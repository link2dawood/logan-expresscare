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
        Schema::create('pre_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->double('amount');
            $table->dateTime('date_time');
            $table->boolean('deduct_from_payroll')->default(1);
            $table->bigInteger('account_id')->unsigned()->nullable()->default(null);
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null')->onUpdate('cascade');
            $table->integer('payroll_month');
            $table->integer('payroll_year');
            $table->longText('notes')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_payments');
    }
};