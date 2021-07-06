<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\MuseumController;


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
    return view('welcome');
});


Route::prefix("museum")->name("museum.")->group(function () {
    Route::get("/", [MuseumController::class, "index"]);
});

Route::get("/artwork/{id}", [ArtworkController::class, "index"])->name('artwork.index');

