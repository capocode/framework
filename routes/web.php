<?php

use Illuminate\Support\Facades\Route;
use Capo\Http\Controllers\CatchAll;

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

Route::any('/{any}', [CatchAll::class, 'index'])->where('any', '.*');
