<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Shift;
use App\Models\StaffMember;
use App\Models\Attendance;
use App\Classes\CommonHrm;
use App\Scopes\CompanyScope;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->boolean('is_next_day')->default(0)->after('self_clocking');
        });

        Schema::table('holidays', function (Blueprint $table) {
            $table->boolean('is_half_day')->default(false)->after('is_weekend');
            $table->string('half_holiday', 20)->default('before_break')->after('is_half_day');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->date('actual_clock_in_date')->nullable()->default(null)->after('reason');
            $table->string('half_leave', 20)->default('no_leave')->after('is_half_day');
            $table->integer('early_clock_in_time')->nullable()->default(null)->after('actual_clock_in_date');
            $table->integer('allow_clock_out_till')->nullable()->default(null)->after('early_clock_in_time');
            $table->integer('late_time_duration')->nullable()->default(0)->after('is_late');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->string('half_leave', 20)->default('before_break')->after('is_half_day');
        });


        $allShifts =  Shift::withoutGlobalScope(CompanyScope::class)->get();
        if ($allShifts) {
            foreach ($allShifts as $shift) {
                if (strtotime($shift->clock_out_time) <= strtotime($shift->clock_in_time)) {
                    $shift->is_next_day = 1;
                } else {
                    $shift->is_next_day = 0;
                }
                $shift->save();
            }
        }

        $allAttendance =  Attendance::withoutGlobalScope(CompanyScope::class)->get();

        if ($allAttendance) {
            foreach ($allAttendance as $attendance) {
                $staff = StaffMember::with(['shift' => function ($query) {
                    return $query->withoutGlobalScope(CompanyScope::class);
                }])->find($attendance->user_id);
                $shift = $staff->shift;
                if ($shift && $attendance->clock_in_time != null) {
                    $attendance->late_time_duration = ($attendance->is_late == 1) ? CommonHrm::getMinutesFromTimes($attendance->office_clock_in_time, $attendance->clock_in_time) : 0;
                    $attendance->actual_clock_in_date = $attendance->date;
                    $attendance->early_clock_in_time = $shift->early_clock_in_time;
                    $attendance->allow_clock_out_till = $shift->allow_clock_out_till;
                    $attendance->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void

    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('half_leave');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('allow_clock_out_till');
            $table->dropColumn('early_clock_in_time');
            $table->dropColumn('half_leave');
            $table->dropColumn('actual_clock_in_date');
        });

        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn('half_holiday');
            $table->dropColumn('is_half_day');
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('is_next_day');
        });
    }
};
