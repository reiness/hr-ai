<?php

use App\Http\Controllers\TestingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ProfileController;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/test', [TestingController::class, 'predict'])->name('test.predict');

    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::post('/batches', [BatchController::class, 'createBatch'])->name('batches.create');
    Route::post('/batches/{batch}/cvs', [BatchController::class, 'uploadCVs'])->name('batches.upload');
    Route::get('/batches/{batch}', [BatchController::class, 'showBatch'])->name('batches.show');

    // Custom profile route
    Route::get('/user/profile', [ProfileController::class, 'show'])->name('profile.show');
});
