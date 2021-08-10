<?php

use App\Http\Controllers\ConfigController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes(['verify' => true]);

Route::get('/', function () {
    return view('welcome');
})->name('dashboard');

Route::get('client/', [HomeController::class, 'client'])->middleware('verified')->name('client');

Route::post('client/addfunds', [HomeController::class, 'addfunds'])->name('addfunds');
Route::post('client/profile', [HomeController::class, 'profile'])->name('profile');
Route::post('client/active/{hub}', [HomeController::class, 'active'])->name('active');

// Route::get('client/', [HomeController::class, 'client'])->name('client.profile');

// Route::get('verification/{id}', function ($id) {
    
// })->name('verification.notice');

Route::get('/home', function () {
    return redirect()->route('admin');
});

Route::get('/admin', [HomeController::class, 'index'])->name('admin');


Route::resource('servers', ServerController::class);
Route::get('servers/delete/{id}', [ServerController::class, 'delete'])->name('servers.delete');
Route::get('servers/{server}/{id}', [ServerController::class, 'serverop'])->name('serverop');
Route::get('servers/netlog', [ServerController::class, 'netlog'])->name('netlog');

Route::resource('users', UserController::class);
Route::get('users/delete/{id}', [UserController::class, 'delete'])->name('users.delete');

Route::resource('hubs', HubController::class);
Route::post('hubs/billing/{hub}', [HubController::class, 'billing'])->name('billing');
Route::get('hubs/delete/{id}', [HubController::class, 'delete'])->name('hubs.delete');

Route::get('settings', [ConfigController::class, 'index'])->name('settings.index');
Route::post('settings/qr', [ConfigController::class, 'qr'])->name('settings.qr');
Route::post('settings/hash', [ConfigController::class, 'hash'])->name('settings.hash');
Route::post('settings/price', [ConfigController::class, 'price'])->name('settings.price');
