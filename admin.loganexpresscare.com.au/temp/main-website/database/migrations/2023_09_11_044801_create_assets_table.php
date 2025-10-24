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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->bigInteger('asset_type_id')->unsigned()->nullable()->default(null);
            $table->foreign('asset_type_id')->references('id')->on('asset_types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('image')->nullable()->default(null);
            $table->string('serial_number')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->bigInteger('location_id')->unsigned()->nullable()->default(null);
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');
            $table->string('status', 20)->default('working');
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('broken_by')->unsigned()->nullable()->default(null);
            $table->foreign('broken_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->bigInteger('account_id')->unsigned()->nullable()->default(null);
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null')->onUpdate('cascade');
            $table->dateTime('purchase_date')->nullable()->default(null);
            $table->double('price')->nullable()->default(0);
            $table->timestamps();
        });

        Schema::create('asset_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('asset_id')->unsigned()->nullable()->default(null);
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('lent_to')->unsigned()->nullable()->default(null);
            $table->foreign('lent_to')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('lent_by')->unsigned()->nullable()->default(null);
            $table->foreign('lent_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('return_by')->unsigned()->nullable()->default(null);
            $table->foreign('return_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('broken_user_id')->unsigned()->nullable()->default(null);
            $table->foreign('broken_user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->text('notes')->nullable()->default(null);
            $table->date('lend_date')->nullable()->default(null);
            $table->date('return_date')->nullable()->default(null);
            $table->date('actual_return_date')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->bigInteger('asset_user_id')->unsigned()->nullable()->default(null);
            $table->foreign('asset_user_id')->references('id')->on('asset_users')->onDelete('SET null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign('assets_asset_user_id_foreign');
            $table->dropColumn([
                'asset_user_id'
            ]);
        });

        Schema::dropIfExists('asset_users');
        Schema::dropIfExists('assets');
    }
};
