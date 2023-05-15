<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\HarvestController;
use App\Http\Controllers\RoleController;
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
    return to_route('login');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::middleware('role:Admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('categories', CategoryController::class);

        Route::prefix('dropdown')->controller(DropdownController::class)->as('dropdown.')->group(function () {
            Route::get('roles', 'getRoles')->name('roles');
            Route::get('farmers', 'getFarmers')->name('farmers');
        });
    });

    Route::resource('harvests', HarvestController::class);
});


Route::prefix('dropdown')->controller(DropdownController::class)->as('dropdown.')->group(function () {
    Route::get('categories', 'getCategories')->name('categories');
    Route::get('provinces', 'getProvinces')->name('provinces');
    Route::get('cities', 'getCities')->name('cities');
    Route::get('subdistricts', 'getSubdistricts')->name('subdistricts');
    Route::get('villages', 'getVillages')->name('villages');
});
