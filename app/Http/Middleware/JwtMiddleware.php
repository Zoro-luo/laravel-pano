<?php

namespace App\Http\Middleware;

use App\Common\Auth\JwtAuth;
use App\Common\Err\ApiErrDesc;
use App\Exceptions\ApiException;
use Closure;

class JwtMiddleware
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
        $token = $request->input('token');

        if($token){
            $jwtAuth = JwtAuth::getInstance();
            $jwtAuth->setToken($token);

            if($jwtAuth->validate() && $jwtAuth->verify()){
                return $next($request);
            }else{
               // throw new ApiException(ApiErrDesc::ERR_TOKEN_EXPIRE);
            }
        }else{
            //throw new ApiException(ApiErrDesc::ERR_PARAMS);
        }


    }
}
