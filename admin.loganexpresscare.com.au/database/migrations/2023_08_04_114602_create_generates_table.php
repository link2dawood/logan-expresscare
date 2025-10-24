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
        Schema::create('generates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('title');
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('letterhead_template_id')->unsigned()->nullable()->default(null);
            $table->foreign('letterhead_template_id')->references('id')->on('letterhead_templates')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('left_space')->nullable()->default(null);
            $table->integer('right_space')->nullable()->default(null);
            $table->integer('top_space')->nullable()->default(null);
            $table->integer('bottom_space')->nullable()->default(null);
            $table->longText('description')->nullable()->default(null);
            $table->bigInteger('admin_id')->unsigned()->nullable()->default(null);
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generates');
    }
};
