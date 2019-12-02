<?php

namespace Laracatch\Client\ViewComposers;

use Illuminate\Contracts\View\View;
use Laracatch\Client\Http\Controllers\ShareErrorController;
use Laracatch\Client\LaracatchConfig;
use Laracatch\Client\Support\TelescopeUrl;

class ErrorPageComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('config', with(new LaracatchConfig())->toArray());

        $view->with('shareEndpoint', action('\Laracatch\Client\Http\Controllers\ShareErrorController'));

        $view->with('telescopeUrl', TelescopeUrl::get());

        $view->with('laracatch_src', file_get_contents(
                __DIR__ . "/../../resources/compiled/laracatcher.js")
        );
    }
}