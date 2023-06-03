<?php

namespace App\Http\Middleware;

use Closure;
use App\Gateway;

class GatewayAuthMiddleware
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
        $userId = $request->header('userId');
        $accessToken = $request->header('accessToken');

        //Verify User Id and Token
        //on Gateway
        $verification = Gateway::where([
                                ['userId', '=', $userId],
                                ['gatewayAccessToken', '=', $accessToken]
                            ])->get();

        if (sizeof($verification) == 1) {
            return $next($request);
        }else {
            return abort(401);
        }
    }
}
