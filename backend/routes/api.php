<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApiDocController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [ApiDocController::class, 'health']);
Route::post('/messages', [ApiController::class, 'store']);
Route::get('/messages', [ApiController::class, 'index']);
