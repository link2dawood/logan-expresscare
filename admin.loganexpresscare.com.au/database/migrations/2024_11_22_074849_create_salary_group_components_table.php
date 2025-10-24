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
        Schema::create('salary_group_components', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('salary_component_id')->unsigned()->nullable()->default(null);
            $table->foreign('salary_component_id')->references('id')->on('salary_components')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('salary_group_id')->unsigned()->nullable()->default(null);
            $table->foreign('salary_group_id')->references('id')->on('salary_groups')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_group_components');
    }
};
