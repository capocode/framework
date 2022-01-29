<?php

use Illuminate\Support\Facades\Route;
use Capo\Services\Capo\Controllers\CatchAll;

Route::any('/{any}', [CatchAll::class, 'index'])->where('any', '.*');
