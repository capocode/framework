<?php

use Illuminate\Support\Facades\Route;
use Capo\Services\Phatsby\Controllers\CatchAll;

Route::any('/{any}', [CatchAll::class, 'index'])->where('any', '.*');
