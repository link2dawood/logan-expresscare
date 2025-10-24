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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('account_id')->unsigned()->nullable()->default(null);
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('deposit_category_id')->unsigned()->nullable()->default(null);
            $table->foreign('deposit_category_id')->references('id')->on('deposit_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->double('amount');
            $table->dateTime('date_time');
            $table->bigInteger('payer_id')->unsigned();
            $table->foreign('payer_id')->references('id')->on('payers')->onDelete('cascade')->onUpdate('cascade');
            $table->string('reference_number')->nullable()->default(null);
            $table->string('file')->nullable()->default(null);
            $table->longText('notes')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
