<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FromNC
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $curUserIP = $request->ip();
        $validIPs = array('10.10.10.', '192.168.', '89.152.242.242', '172.16.', '172.17.', '172.18.', '172.19.',
            '172.20.', '172.21.', '172.22.', '172.23.', '172.24.', '172.25.', '172.26.', '172.27.', '172.28.',
            '172.29.', '172.30.', '172.31.', '10.');
        if (
            !in_array(substr($curUserIP, 0, 9), $validIPs)
            && !in_array(substr($curUserIP, 0, 8), $validIPs)
            && !in_array(substr($curUserIP, 0, 7), $validIPs)
            && !in_array(substr($curUserIP, 0, 3), $validIPs)
            && !in_array($curUserIP, $validIPs)
        ) {
            abort(403);
        }

        return $next($request);
    }
}
