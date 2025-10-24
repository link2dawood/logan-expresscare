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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('title');
            $table->dateTime('date_time')->nullable()->default(null);
            $table->bigInteger('from_user_id')->unsigned()->nullable()->default(null);
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('to_user_id')->unsigned()->nullable()->default(null);
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('letterhead_template_id')->unsigned()->nullable()->default(null);
            $table->foreign('letterhead_template_id')->references('id')->on('letterhead_templates')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('generates_id')->unsigned()->nullable()->default(null);
            $table->foreign('generates_id')->references('id')->on('generates')->onDelete('set null')->onUpdate('cascade');
            $table->string('status', 20)->default('pending');
            $table->text('description')->nullable()->default(null);
            $table->string('proff_of_document')->nullable()->default(null);
            $table->bigInteger('manager_id')->unsigned()->nullable()->default(null);
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->text('reply_notes')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
