<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\IpValidationMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{pathMatch}', function(){
    return view('welcome');
})->where('pathMatch', ".*");

Route::middleware([IpValidationMiddleware::class])->group(function () {
    Route::apiResource('ip-lists', IpListController::class);
});

Route::post('/auto-login', [AuthController::class, 'autoLogin']);

Route::get('/login-ip', function () {
    return response()->json(['ip' => request()->ip()]);
});

Route::post('/update-session', [AuthController::class, 'updateSession']);
