<?php

use App\Http\Controllers\API\CrewController;
use App\Http\Controllers\API\GenreController;
use App\Http\Controllers\API\MovieController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::resource('genres', GenreController::class);
Route::resource('crews', CrewController::class);
Route::resource('movies', MovieController::class);
Route::get('/search', [MovieController::class, 'search']);
