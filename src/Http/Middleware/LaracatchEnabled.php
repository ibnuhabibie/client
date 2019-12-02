<?php

namespace Laracatch\Client\Http\Middleware;

use Closure;

class LaracatchEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->enabled()) {
            abort(404);
        }

        return $next($request);
    }

    /**
     * Check if Laracatch is enabled
     *
     * @return boolean
     */
    protected function enabled(): bool
    {
        $enabled = value(config()->get('laracatch.enabled'));

        if ($enabled === null) {
            $enabled = config()->get('app.debug');
        }

        return $enabled && ! app()->environment('testing');
    }
}
