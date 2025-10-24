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
        Schema::create('employee_specific_leave_counts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('leave_type_id')->unsigned()->nullable()->default(null);
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('total_leaves')->default(0);
            $table->timestamps();
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->string('expense_for', 25)->default('all')->after('name');
        });

        Schema::table('leave_types', function (Blueprint $table) {
            $table->string('count_type')->default('same_for_all');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_specific_leave_counts');

        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn('count_type');
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn('expense_for');
        });
    }
};
