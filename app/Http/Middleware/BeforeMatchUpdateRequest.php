<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class BeforeMatchUpdateRequest
 * @package App\Http\Middleware
 */
class BeforeMatchUpdateRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $input = $request->all();

        array_set($input, 'match.id', $request->route('matchId'));

        $request->replace($input);

        return $next($request);
    }
}
