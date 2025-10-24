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
        Schema::create('feedback_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('feedback_id')->unsigned()->nullable()->default(null);
            $table->foreign('feedback_id')->references('id')->on('feedbacks')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->longText('data')->nullable()->default(null);
            $table->boolean('feedback_given')->default(false);
            $table->boolean('allowed')->default(false);
            $table->date('submit_date')->nullable()->default(null);
            $table->string('rating')->nullable()->default(null);
            $table->longText('rating_details')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_users');
    }
};