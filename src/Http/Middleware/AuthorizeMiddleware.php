<?php

namespace Outl1ne\PageManager\Http\Middleware;

use Outl1ne\PageManager\PageManager;

class AuthorizeMiddleware
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return resolve(PageManager::class)->authorize($request) ? $next($request) : abort(403);
    }
}
