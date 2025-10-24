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
        Schema::create('pdf_fonts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null')->onUpdate('cascade');
            $table->string('name');
            $table->string('file')->nullable()->default(NULL);;
            $table->tinyInteger('user_kashida')->nullable()->default(0);
            $table->unsignedSmallInteger('use_otl')->nullable()->default(255);
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->bigInteger('pdf_font_id')->unsigned()->nullable()->default(null);
            $table->foreign('pdf_font_id')->references('id')->on('pdf_fonts')->onDelete('set null')->onUpdate('cascade');
            $table->boolean('use_custom_font')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign('companies_pdf_font_id_foreign');
            $table->dropColumn('pdf_font_id');
            $table->dropColumn('use_custom_font');
        });

        Schema::dropIfExists('pdf_fonts');
    }
};