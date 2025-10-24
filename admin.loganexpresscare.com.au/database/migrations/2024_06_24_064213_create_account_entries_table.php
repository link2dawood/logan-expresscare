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
        Schema::create('account_entries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('account_id')->unsigned()->nullable()->default(null);
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade')->onUpdate('cascade');
            $table->double('amount');
            $table->boolean('is_debit')->default(1);
            $table->string('type', 20)->default(null);
            $table->date('date');
            $table->bigInteger('deposit_id')->unsigned()->nullable()->default(null);
            $table->foreign('deposit_id')->references('id')->on('deposits')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('expense_id')->unsigned()->nullable()->default(null);
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('payroll_id')->unsigned()->nullable()->default(null);
            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('pre_payment_id')->unsigned()->nullable()->default(null);
            $table->foreign('pre_payment_id')->references('id')->on('pre_payments')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('asset_id')->unsigned()->nullable()->default(null);
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('appreciation_id')->unsigned()->nullable()->default(null);
            $table->foreign('appreciation_id')->references('id')->on('appreciations')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('initial_account_id')->unsigned()->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_entries');
    }
};
