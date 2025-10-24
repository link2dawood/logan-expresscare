<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('bill')->nullable()->default(NULL);
            $table->bigInteger('expense_category_id')->unsigned()->nullable()->default(null);
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('payee_id')->unsigned()->nullable()->default(null);
            $table->foreign('payee_id')->references('id')->on('payees')->onDelete('cascade')->onUpdate('cascade');
            $table->float('amount', 8, 2);
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->string('notes', 1000)->nullable()->default(null);
            $table->dateTime('date_time');
            $table->string('reference_number')->nullable()->default(null);
            $table->bigInteger('account_id')->unsigned()->nullable()->default(null);
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null')->onUpdate('cascade');
            $table->string('status', 20)->default('pending');
            $table->string('expense_type', 20)->nullable()->default('employee_claims'); //[employee_claims,company_expenses]
            $table->dateTime('payment_date')->nullable()->default(null);
            $table->boolean('payment_status')->nullable()->default(false);
            $table->integer('payroll_month')->nullable()->default(null);
            $table->integer('payroll_year')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
