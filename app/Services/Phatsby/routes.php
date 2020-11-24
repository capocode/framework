<?php

use Illuminate\Support\Facades\Route;
use App\Services\Phatsby\Controllers\CatchAll;

Route::any('/{any}', [CatchAll::class, 'index'])->where('any', '.*');
