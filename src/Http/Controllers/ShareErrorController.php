<?php

namespace Laracatch\Client\Http\Controllers;

use Laracatch\Client\Actions\ShareErrorAction;
use Laracatch\Client\Exceptions\ShareErrorException;
use Laracatch\Client\Http\Requests\ShareErrorRequest;

class ShareErrorController
{
    /**
     * Handle the share error request.
     *
     * @param ShareErrorRequest $request
     * @param ShareErrorAction $shareAction
     *
     * @return array|false
     */
    public function __invoke(ShareErrorRequest $request, ShareErrorAction $shareAction)
    {
        try {
            return $shareAction->handle(
                json_decode($request->get('error'), true),
                $request->get('tabs'),
                $request->get('lineSelection')
            );
        } catch (ShareErrorException $exception) {
            abort(500, 'Unable to share the error ' . $exception->getMessage());
        }

        return false;
    }
}
