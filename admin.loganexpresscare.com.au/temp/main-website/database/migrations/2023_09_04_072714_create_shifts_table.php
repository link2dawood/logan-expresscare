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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->time('clock_in_time');
            $table->time('clock_out_time');
            $table->integer('late_mark_after')->nullable()->default(null);
            $table->integer('early_clock_in_time')->nullable()->default(null);
            $table->integer('allow_clock_out_till')->nullable()->default(null);
            $table->boolean('self_clocking')->default(1);
            $table->string('allowed_ip_address', 1000)->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
