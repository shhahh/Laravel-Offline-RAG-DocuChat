<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::get('/docu-chat', [DocumentController::class, 'index']);
Route::post('/docu-upload', [DocumentController::class, 'upload'])->name('doc.upload');
Route::post('/doc-chat', [DocumentController::class, 'askPdf'])->name('doc.chat');

Route::get('/', function () {
    return view('welcome');
});
