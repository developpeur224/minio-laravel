<?php

use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('media.index');
});

Route::prefix('media')->name('media.')->group(function () {
    Route::get('/', [MediaController::class, 'index'])->name('index');
    Route::get('/create', [MediaController::class, 'create'])->name('create');
    Route::post('/', [MediaController::class, 'store'])->name('store');
    Route::get('/images', [MediaController::class, 'images'])->name('images');
    Route::get('/videos-documents', [MediaController::class, 'videosAndDocuments'])->name('videos-documents');
    Route::get('/{id}/edit', [MediaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [MediaController::class, 'update'])->name('update');
    Route::delete('/{id}', [MediaController::class, 'destroy'])->name('destroy');
});

Route::get('/media/{id}/download', [MediaController::class, 'download'])->name('media.download');