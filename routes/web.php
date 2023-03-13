<?php

use Illuminate\Support\Facades\Route;
use Labdacaraka\WebInstaller\Controllers\InstallerController;
use Labdacaraka\WebInstaller\Middlewares\RedirectIfInstalled;

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
//
//Route::get('/install', function () {
//});

// Route group for web installer
Route::group(['prefix' => 'install', 'as' => 'web-installer.', 'middleware' => ['web', RedirectIfInstalled::class]], function () {
    Route::get('/', [InstallerController::class, 'index'])->name('welcome');
    Route::post('/validate-purchase-code', [InstallerController::class, 'validatePurchaseCode'])->name('validate-purchase-code');
    Route::get('/check-requirements', [InstallerController::class, 'checkRequirements'])->name('check-requirements');
    Route::get('/check-permissions', [InstallerController::class, 'checkPermissions'])->name('check-permissions');
    Route::get('/app-settings', [InstallerController::class, 'appSettings'])->name('app-settings');
    Route::post('/app-settings', [InstallerController::class, 'appSettingStore'])->name('app-setting-store');
    Route::get('/database-settings', [InstallerController::class, 'databaseSettings'])->name('database-settings');
    Route::post('/database-settings', [InstallerController::class, 'databaseSettingStore'])->name('database-settings-store');
    Route::post('/database', [InstallerController::class, 'saveDatabase'])->name('database.save');
    Route::get('/final', [InstallerController::class, 'final'])->name('final');
    Route::post('/run', [InstallerController::class, 'runInstall'])->name('run-install');
    Route::get('/run', [InstallerController::class, 'runInstall'])->name('run-install-get');
});
