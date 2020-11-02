<?php

namespace Energibangsa\Cepet\middlewares;

use Closure;

class CheckAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $forbidden = $request->session()->get('access')['forbidden'];

        $uri =  $request->path();
        if (in_array($uri, $forbidden)) {
            return abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
