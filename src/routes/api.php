<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductIndexController;

Route::post('/search/products/reindex', [ProductIndexController::class, 'reindexAsync']);
Route::get('/search/products/reindex/{job}', [ProductIndexController::class, 'reindexStatus']);
Route::get('/probe', fn() => response()->json(['ok'=>true]));
