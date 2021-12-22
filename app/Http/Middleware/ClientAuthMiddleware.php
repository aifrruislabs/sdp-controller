<?php

namespace App\Http\Middleware;

use Closure;
use App\Client;

class ClientAuthMiddleware
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
        $clientId = $request->header('clientId');
        $clientToken = $request->header('clientToken');

        //Verify Client Id and Access Token
        $verification = Client::where([
                                ['clientId', '=', $clientId],
                                ['accessToken', '=', $clientToken]
                            ])->get();

        if (sizeof($verification) == 1) {
            return $next($request);
        }else {
            return abort(401);
        }
    }
}
