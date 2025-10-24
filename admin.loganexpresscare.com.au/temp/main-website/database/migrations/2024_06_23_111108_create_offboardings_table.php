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
        Schema::create('offboardings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('manager_id')->unsigned()->nullable()->default(null);
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('letterhead_template_id')->unsigned()->nullable()->default(null);
            $table->foreign('letterhead_template_id')->references('id')->on('letterhead_templates')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('generates_id')->unsigned()->nullable()->default(null);
            $table->foreign('generates_id')->references('id')->on('generates')->onDelete('set null')->onUpdate('cascade');
            $table->dateTime('submit_date')->nullable()->default(null);
            $table->dateTime('start_date')->nullable()->default(null);
            $table->dateTime('end_date')->nullable()->default(null);
            $table->string('title');
            $table->text('description');
            $table->string('type', 50);
            $table->string('status', 10)->nullable()->default('pending');
            $table->text('reply_notes')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offboardings');
    }
};
