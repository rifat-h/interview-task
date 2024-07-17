<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModuleController;
use App\Livewire\HomePage;

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


Auth::routes([
    'register' => true,
    'verify' => true,
    'reset' => false,
    'confirm' => false,
    'confirm-password' => false,
]);


// ------------ api start ----------


// ------------ api end ----------


// ------------ front start ----------
Route::get('/', HomePage::class)->name('front.index');
// ------------ front end ----------


// ------------ dashboard start ----------
Route::prefix('dashboard')->middleware(['auth', 'email_verified', 'withMenu', 'authorizationChecker'])->group(function () {

    // dashboard
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // change own password
    Route::get('user/changepassword', [UserController::class, 'ChangeOwnPassView'])->name('cp.view');

    // change user password
    Route::get('user/change-password/{user}', [UserController::class, 'ChangePassView'])->name('changepassword.view');
    Route::post('user/change-password/{user}', [UserController::class, 'ChangePass'])->name('changepassword');

    // change user status
    Route::get('user/toggle-status/{user}', [UserController::class, 'toggleStatus'])->name('user.toggle.status');
    Route::resource('user', UserController::class);

    // role
    Route::resource('role', RoleController::class)->except(['show']);

    // menu
    Route::resource('module', ModuleController::class)->except(['show']);
    Route::get('action-menu/{menuId}/create', [ModuleController::class, 'actionMenuCreate'])->name('actionmenu.create');
    Route::get('action-menu/{menuId}', [ModuleController::class, 'actionMenuIndex'])->name('actionmenu.index');

    // site settings
    Route::get('/site-settings', [HomeController::class, 'siteSettingView'])->name('site_setting.index');
    Route::patch('/site-settings-update', [HomeController::class, 'siteSetting'])->name('site.setting');

});
// ------------ dashboard end ----------




// ------------ utility start ----------

Route::get('/clear', function () {

    // Artisan::call('cache:clear');
    // Artisan::call('config:clear');
    // Artisan::call('config:cache');
    // Artisan::call('view:clear');
    // Artisan::call('route:clear');
    // Artisan::call('clear-compiled');

    Artisan::call('optimize:clear');
    Artisan::call('optimize');

    return "Cleared!";
});


// ------------ utility start ----------

Route::get('/storage-link', function () {

    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/public/storage';

    if (is_link($linkFolder)) {
        unlink($linkFolder);
    }

    Artisan::call('storage:link');

});

// ------------ utility end ----------
