<?php

namespace Capo\Services\Phatsby\Controllers;

use Illuminate\Http\Request;
use Capo\Http\Controllers\Controller;

class CatchAll extends Controller
{
    public function index(Request $request, ?string $path = 'index')
    {
        // Check pages for route
        $pagesPath = 'pages/' . $path;

        if (view()->exists($pagesPath)) {
            return view($pagesPath);
        }

        return abort(404);
    }
}
