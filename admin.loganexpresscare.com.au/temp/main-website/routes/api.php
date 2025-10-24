<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WorkDurationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['api', 'auth:api']], function () {
    Route::get('/user', function () {
        return response()->json(auth()->user());
    });
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
});
