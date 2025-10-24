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
        Schema::create('increments_promotions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('type')->default('promotion'); //increament,promotion,increament_promotion
            $table->date('date');
            $table->text('description')->nullable()->default(null);;
            $table->integer('net_salary')->nullable();
            $table->bigInteger('promoted_designation_id')->unsigned()->nullable()->default(null);
            $table->foreign('promoted_designation_id')->references('id')->on('designations')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('current_designation_id')->unsigned()->nullable()->default(null);
            $table->foreign('current_designation_id')->references('id')->on('designations')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('generate_id')->unsigned()->nullable()->default(null);
            $table->foreign('generate_id')->references('id')->on('generates')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('letterhead_template_id')->unsigned()->nullable()->default(null);
            $table->foreign('letterhead_template_id')->references('id')->on('letterhead_templates')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('increments_promotions');
    }
};
