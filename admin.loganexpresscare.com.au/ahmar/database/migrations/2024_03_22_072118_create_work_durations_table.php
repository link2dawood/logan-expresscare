<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Attendance;
use App\Classes\CommonHrm;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_durations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('attendance_id')->unsigned()->nullable()->default(null);
            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade')->onUpdate('cascade');
            $table->time('start_time')->nullable()->default(null);
            $table->time('end_time')->nullable()->default(null);
            $table->integer('duration')->nullable()->default(null);
            $table->text('notes')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);
            $table->timestamps();
        });

        $allAttendance = Attendance::withoutGlobalScope(CompanyScope::class)->get();

        if ($allAttendance) {
            foreach ($allAttendance as $attendance) {
                if ($attendance->clock_in_time != null) {
                    $totalDuration = $attendance->clock_out_time != null ? CommonHrm::getMinutesFromTimes($attendance->clock_in_time, $attendance->clock_out_time) : CommonHrm::getMinutesFromTimes($attendance->clock_in_time, $attendance->office_clock_out_time);
                    DB::table('work_durations')->insert(
                        [
                            'company_id'    => $attendance->company_id,
                            'attendance_id' => $attendance->id,
                            'start_time'    => $attendance->clock_in_time,
                            'end_time'      => $attendance->clock_out_time != null ? $attendance->clock_out_time : $attendance->office_clock_out_time,
                            'duration'      => $totalDuration,
                            'status'        => 'started',
                            'notes'         => null,
                            'created_at'    => $attendance->created_at,
                            'updated_at'    => $attendance->updated_at,
                        ]
                    );
                    $attendance->total_duration = $totalDuration;
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
        Schema::dropIfExists('work_durations');
    }
};
