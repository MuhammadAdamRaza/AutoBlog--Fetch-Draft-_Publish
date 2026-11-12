<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class UseBuiltAssetsForTunnel
{
    /**
     * Serve compiled Vite assets instead of the local dev server hot file
     * when the request arrives via a non-local tunnel host such as ngrok.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->getHost(), ['localhost', '127.0.0.1', '::1'], true)) {
            Vite::useHotFile(storage_path('framework/vite.hot.disabled'));
        } else {
            Vite::useHotFile(public_path('hot'));
        }

        return $next($request);
    }
}
