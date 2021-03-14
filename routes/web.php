<?php

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
    return redirect()->route('login');
});

Auth::routes();

Route::get('translation/{locale}', [\App\Http\Controllers\Controller::class, 'translations']);

Route::group(['middleware' => 'auth'], function () {
    // Auth users

    Route::group(['prefix' => 'admin', 'middleware' => 'role', 'roles' => ['administrator']], function () {
        // Administrator
        Route::post('user/exists', [\App\Http\Controllers\UserController::class, 'exists']);
        Route::resource('user', \App\Http\Controllers\UserController::class);
    });

    Route::group([
        'prefix' => 'manager',
        'middleware' => 'role',
        'roles' => ['administrator', 'inventory_manager']
    ], function () {
        // Inventory manager
        Route::get('digital-inventory/{digital_inventory}/excel', [\App\Http\Controllers\DigitalInventoryController::class, 'excel']);
        Route::resource('digital-inventory', \App\Http\Controllers\DigitalInventoryController::class);
    });

    Route::group([
        'prefix' => 'warehouse',
        'middleware' => 'role',
        'roles' => ['administrator', 'inventory_manager', 'warehouse']
    ], function () {
        // Everybody
        Route::get('user/config', [\App\Http\Controllers\UserController::class, 'config'])->name('user.config');
        Route::put('user/config', [\App\Http\Controllers\UserController::class, 'updateConfig'])->name('user.updateConfig');

        Route::post('inventory/{inventory}/observation', [\App\Http\Controllers\InventoryController::class, 'observation']);
        Route::resource('inventory', \App\Http\Controllers\InventoryController::class);
        Route::resource('product', \App\Http\Controllers\ProductController::class);
        Route::get('product-serial/{product}/product', [\App\Http\Controllers\ProductSerialController::class, 'byProduct']);
        Route::resource('product-serial', \App\Http\Controllers\ProductSerialController::class);
    });

});
