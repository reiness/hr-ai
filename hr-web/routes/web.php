<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;

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

    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::post('/batches', [BatchController::class, 'createBatch'])->name('batches.create');
    Route::post('/batches/{batch}/cvs', [BatchController::class, 'uploadCVs'])->name('batches.upload');
    Route::get('/batches/{batch}', [BatchController::class, 'showBatch'])->name('batches.show');
    Route::delete('/batches/{id}', [BatchController::class, 'destroy'])->name('batches.destroy');
    Route::delete('/batches/{batch}/cvs', [BatchController::class, 'deleteCVs'])->name('cvs.delete');
    Route::get('/batches/{batch}/downloadAll', [BatchController::class, 'downloadAll'])->name('batches.downloadAll');


    // Routes for processing batches
    Route::get('/processed-batches', [BatchController::class, 'processedBatches'])->name('batches.processed');
    Route::post('/batches/{batch}/process', [BatchController::class, 'processBatch'])->name('batches.process');
    Route::get('/processed-batches/{processedBatch}', [BatchController::class, 'showProcessedBatch'])->name('processedBatches.show');
    Route::delete('/processedBatches/{id}', [BatchController::class, 'destroyProcessedBatch'])->name('processedBatches.destroy');
    Route::delete('/processedBatches/{id}/cvs', [BatchController::class, 'deleteProcessedCVs'])->name('processedBatches.deleteCVs');
    Route::get('/processedBatches/{id}/downloadAll', [BatchController::class, 'downloadAllProcessed'])->name('processedBatches.downloadAll');


    
});
