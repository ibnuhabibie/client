<?php

if (! function_exists('ddd')) {
    function ddd() {
        dump(...func_get_args());

        /**
         * @var $handler \Laracatch\Client\LaracatchHandler
         */
        $handler = app('laracatch.handler');
        $errorModel = $handler->buildFromMessage('Dump!');

        echo $handler->getHtmlResponse($errorModel, 'DebugTab', [
            'dump' => true,
            'breadcrumb' => false,
            'log' => false,
            'event' => false
        ]);

        exit;
    }
}