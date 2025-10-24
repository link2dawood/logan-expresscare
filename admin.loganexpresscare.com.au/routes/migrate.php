<?php

use App\Classes\Common;
use App\Classes\LangTrans;
use App\Classes\PermsSeed;
use App\Models\Company;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/{path?}', function () {

    // For Running Migration
    PermsSeed::seedMainPermissions();
    LangTrans::seedMainTranslations();
    Artisan::call('migrate', ['--force' => true]);

    // Delete predeined templates which are not used
    DB::table('settings')->whereIn('setting_type', [
        'staff_member_create',
        'staff_member_update',
        'staff_member_delete'
    ])->delete();

    // For settings
    $allCompanies = Company::withoutGlobalScope('company')->get();

    foreach ($allCompanies as $company) {
        // Insert default settings for each company
        Common::insertInitSettings($company);
    }

    // Config clear
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');

    // Deleting migrate file
    File::delete(public_path() . '/migrate');

    return redirect('/');
})->where('path', '.*')->name('migrate');
