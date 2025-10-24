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
            $table->bigInteger('department_id')->unsigned()->nullable()->default(null)->after('time_format');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('designation_id')->unsigned()->nullable()->default(null)->after('department_id');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('shift_id')->unsigned()->nullable()->default(null)->after('designation_id');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_department_id_foreign');
            $table->dropForeign('users_designation_id_foreign');
            $table->dropForeign('users_shift_id_foreign');
            $table->dropColumn('department_id');
            $table->dropColumn('designation_id');
            $table->dropColumn('shift_id');
        });
    }
};
