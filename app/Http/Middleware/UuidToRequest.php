<?php

namespace App\Http\Middleware;

use Closure;

class UuidToRequest
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
        if ($request->header('uuid')) {
            $request->merge(['uuid' => $request->header('uuid')]);
        }
        return $next($request);
    }
}