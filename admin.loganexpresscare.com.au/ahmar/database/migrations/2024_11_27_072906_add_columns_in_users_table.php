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
        Schema::table('users', function (Blueprint $table) {
            $table->float('monthly_amount')->nullable()->default(null);
            $table->float('annual_amount')->nullable()->default(null);
            $table->float('annual_ctc')->nullable()->default(null);
            $table->integer('ctc_value')->nullable()->default(null);
            $table->float('special_allowances')->nullable()->default(null);
            $table->enum('calculation_type', ['fixed', '%_of_ctc'])->default('%_of_ctc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('calculation_type');
            $table->dropColumn('special_allowances');
            $table->dropColumn('ctc_value');
            $table->dropColumn('annual_ctc');
            $table->dropColumn('annual_amount');
            $table->dropColumn('monthly_amount');
        });
    }
};
