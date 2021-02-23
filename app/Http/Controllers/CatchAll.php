<?php

namespace Capo\Http\Controllers;

use Illuminate\Http\Request;

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
