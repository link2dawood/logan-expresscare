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
        Schema::table('leaves', function (Blueprint $table) {
            $table->float('total_days', 8, 2)->default(0)->change();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('clock_in_ip_address')->nullable()->default(null)->change();
            $table->string('clock_out_ip_address')->nullable()->default(null)->change();
        });

        Schema::table('attendances', function (Blueprint $table) {
            // Location
            $table->decimal('clock_in_latitude', 18, 15)->nullable()->default(null);
            $table->decimal('clock_in_longitude', 18, 15)->nullable()->default(null);
            $table->float('clock_in_location_accuracy')->nullable()->default(null);      // in meters
            $table->text('clock_in_location_name')->nullable()->default(null);           // optional: full address from reverse geocoding

            // Device/browser info
            $table->string('clock_in_browser')->nullable()->default(null);
            $table->string('clock_in_platform')->nullable()->default(null);
            $table->string('clock_in_device_type')->nullable()->default(null);           // mobile/desktop/tablet
            $table->text('clock_in_user_agent')->nullable()->default(null);

            // Location
            $table->decimal('clock_out_latitude', 18, 15)->nullable()->default(null);
            $table->decimal('clock_out_longitude', 18, 15)->nullable()->default(null);
            $table->float('clock_out_location_accuracy')->nullable()->default(null);      // in meters
            $table->text('clock_out_location_name')->nullable()->default(null);           // optional: full address from reverse geocoding

            // Device/browser info
            $table->string('clock_out_browser')->nullable()->default(null);
            $table->string('clock_out_platform')->nullable()->default(null);
            $table->string('clock_out_device_type')->nullable()->default(null);           // mobile/desktop/tablet
            $table->text('clock_out_user_agent')->nullable()->default(null);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->boolean('capture_location')->default(false);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('capture_location')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->integer('total_days')->default(0)->change();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('clock_in_ip_address', 20)->nullable()->default(null)->change();
            $table->string('clock_out_ip_address', 20)->nullable()->default(null)->change();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('clock_in_latitude');
            $table->dropColumn('clock_in_longitude');
            $table->dropColumn('clock_in_location_accuracy');
            $table->dropColumn('clock_in_location_name');

            $table->dropColumn('clock_in_browser');
            $table->dropColumn('clock_in_platform');
            $table->dropColumn('clock_in_device_type');
            $table->dropColumn('clock_in_user_agent');

            $table->dropColumn('clock_out_latitude');
            $table->dropColumn('clock_out_longitude');
            $table->dropColumn('clock_out_location_accuracy');
            $table->dropColumn('clock_out_location_name');

            $table->dropColumn('clock_out_browser');
            $table->dropColumn('clock_out_platform');
            $table->dropColumn('clock_out_device_type');
            $table->dropColumn('clock_out_user_agent');
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('capture_location');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('capture_location');
        });
    }
};
