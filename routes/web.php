<?php

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

Route::get('/', function () {
    return view('welcome');
})->name('dashboard');

Auth::routes();

Route::get('verification/{id}', function ($id) {
    
})->name('verification.notice');

Route::get('/home', function () {
    return redirect()->route('admin');
});

Route::get('/admin', [HomeController::class, 'index'])->name('admin');

Route::get('servers/delete/{id}', [ServerController::class, 'delete'])->name('servers.delete');
Route::get('servers/{server}/{id}', [ServerController::class, 'serverop'])->name('serverop');
Route::resource('servers', ServerController::class);

Route::get('users/delete/{id}', [UserController::class, 'delete'])->name('users.delete');
Route::resource('users', UserController::class);

Route::post('hubs/billing/{hub}', [HubController::class, 'billing'])->name('billing');
Route::get('hubs/delete/{id}', [HubController::class, 'delete'])->name('hubs.delete');
Route::resource('hubs', HubController::class);
