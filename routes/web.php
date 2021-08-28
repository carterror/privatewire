<?php

use App\Http\Controllers\ConfigController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\UserController;
use App\Mail\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::post('/contact', function (Request $request) {

    $array = [
        "name" => $request->name,
        'email' => $request->email,
        'commentc' => $request->commentc,
    ];

    Mail::to(Storage::disk('config')->get('email'))->send(new Contact($array));
    return back()->with(['type' => 'success'])->with(['message' => 'Comment sent']);


})->name('contact');

Route::get('client/', [HomeController::class, 'client'])->middleware('verified')->name('client');

Route::get('client/conf/{id}/download', [ImagesController::class, 'confload'])->name('confload');
Route::get('client/conf/{id}/image', [ImagesController::class, 'confimage'])->name('confimage');

Route::get('client/download', [HomeController::class, 'download'])->name('download');
Route::post('client/addfunds/{wallet}', [HomeController::class, 'addfunds'])->name('addfunds');
Route::post('client/addwallet', [HomeController::class, 'addwallet'])->name('addwallet');
Route::post('client/profile', [HomeController::class, 'profile'])->name('profile');
Route::get('client/{id}/delete', [HomeController::class, 'delete'])->name('profile.delete');
Route::post('client/active/{hub}', [HomeController::class, 'active'])->name('active');


Route::get('/home', function () {
    return redirect()->route('admin');
})->name('home');

Route::get('/admin', [HomeController::class, 'index'])->name('admin');

Route::get('servers/expire', [ServerController::class, 'expire'])->name('expire');
Route::get('servers/netlog', [ServerController::class, 'netlog'])->name('netlog');
Route::resource('servers', ServerController::class);
Route::get('servers/delete/{id}', [ServerController::class, 'delete'])->name('servers.delete');
Route::get('servers/{server}/{id}', [ServerController::class, 'serverop'])->name('serverop');

Route::resource('users', UserController::class)->except(['update']);
Route::post('users/update', [UserController::class, 'update'])->name('users.update');
Route::post('users/{id}/ballance', [UserController::class, 'ballance'])->name('users.ballance');
Route::get('users/{id}/delete', [UserController::class, 'delete'])->name('users.delete');

Route::resource('hubs', HubController::class);
Route::post('hubs/billing/{hub}', [HubController::class, 'billing'])->name('billing');
Route::get('hubs/delete/{id}', [HubController::class, 'delete'])->name('hubs.delete');

Route::get('settings', [ConfigController::class, 'index'])->name('settings.index');
Route::post('settings/qr', [ConfigController::class, 'qr'])->name('settings.qr');
Route::post('settings/hash', [ConfigController::class, 'hash'])->name('settings.hash');
Route::post('settings/price', [ConfigController::class, 'price'])->name('settings.price');
Route::get('settings/downloads', [ConfigController::class, 'getdownloads'])->name('settings.downloads');
Route::post('settings/postdownloads', [ConfigController::class, 'postdownloads'])->name('settings.postdownloads');
Route::get('settings/delete/{id}', [ConfigController::class, 'delete'])->name('settings.delete');