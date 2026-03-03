<?php

use Illuminate\Support\Facades\Route;
use Modules\Assessment\Http\Controllers\AssessmentController;
use Modules\Assessment\Http\Controllers\MasterTestController;

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

Route::middleware(['web'])->group(function () {
    Route::middleware(['checkadmin'])->group(function () {

        Route::prefix('admin')->group(function() {

            Route::prefix('Assessment')->group(function() {

                Route::prefix('MasterAssessment')->group(function() {

                    Route::prefix('Test')->group(function() {
                        Route::get('/', [MasterTestController::class, 'index'])->name('admin.mastertest.show');
                        Route::get('/tabel-test', [MasterTestController::class, 'tabel_test'])->name('admin.mastertest.tabel');
                        Route::post('/save-test', [MasterTestController::class, 'store'])->name('admin.mastertest.save');
                    });

                });

            });

        });

    });
});
