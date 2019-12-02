<?php

namespace Laracatch\Client\Http\Controllers;

use Illuminate\Http\Request;

class NavigatorController
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function __invoke(Request $request)
    {
        return view()->make('laracatch::navigator');
    }
}
