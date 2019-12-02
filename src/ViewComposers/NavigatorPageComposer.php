<?php

namespace Laracatch\Client\ViewComposers;

use Illuminate\Contracts\View\View;
use Laracatch\Client\Http\Controllers\ErrorApiController;
use Laracatch\Client\Http\Controllers\ShareErrorController;
use Laracatch\Client\LaracatchConfig;

class NavigatorPageComposer
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
        $view->with('title', 'Laracatch Navigator');

        $view->with('config', with(new LaracatchConfig())->toArray());

        $view->with('endpoint', action('\Laracatch\Client\Http\Controllers\ErrorApiController@index'));

        $view->with('shareEndpoint', action('\Laracatch\Client\Http\Controllers\ShareErrorController'));

        $view->with(
            'laracatch_src',
            file_get_contents(
            __DIR__ . "/../../resources/compiled/laracatcher.js"
        )
        );

        $view->with(
            'navigate_src',
            file_get_contents(
            __DIR__ . "/../../resources/compiled/navigate.js"
        )
        );
    }
}
