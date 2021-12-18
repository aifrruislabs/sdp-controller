<?php

namespace App\Http\Middleware;

use Closure;

use App\User;

class UserAuthMiddleware
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
        $authToken = $request->header('authToken');

        if (empty($userId)) {
            $userId = $request->userId;
        }

        if (empty($authToken)) {
            $authToken = $request->authToken;
        }

        //Verify User Id and Token
        $verification = User::where([
                                ['id', '=', $userId],
                                ['authToken', '=', $authToken]
                            ])->get();

        if (sizeof($verification) == 1) {
            return $next($request);
        }else {
            return abort(401);
        }
    }
}
