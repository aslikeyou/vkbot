<?php

namespace App\Http\Middleware;

use Closure;

class VeryBasicMiddleware
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

        if($request->getUser() != 'tania' || $request->getPassword() != 'privet'){
            $headers = array('WWW-Authenticate' => 'Basic');
            return response('Invalid credentials.',401, $headers);
        }
        return $next($request);
    }
}
