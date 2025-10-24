<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('google_geo_coding_api_key')->nullable()->default(null);
            $table->integer('export_pdf_left_space')->nullable()->default(15);
            $table->integer('export_pdf_right_space')->nullable()->default(15);
            $table->integer('export_pdf_top_space')->nullable()->default(40);
            $table->integer('export_pdf_bottom_space')->nullable()->default(20);
            $table->integer('export_pdf_line_height')->nullable()->default(25);
            $table->integer('export_pdf_font_size')->nullable()->default(14);
        });

        DB::table('users')->where('user_type', 'staff_members')->where('status', 'enabled')->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('google_geo_coding_api_key');
            $table->dropColumn('export_pdf_left_space');
            $table->dropColumn('export_pdf_right_space');
            $table->dropColumn('export_pdf_top_space');
            $table->dropColumn('export_pdf_bottom_space');
            $table->dropColumn('export_pdf_line_height');
            $table->dropColumn('export_pdf_font_size');
        });
    }
};
