<?php

namespace App\Http\Middleware;

use Closure;

class AuthMember
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * 检测会员是否登陆,没用登陆跳转到登陆页面 [guest 来宾用户]
         * auth()->guard('member')->guest() 等价于 (!auth()->guard('member')->check())
         */
        if (auth()->guard('member')->guest()) {
            //将当前url放入session中 登录成功 回到此url中
            $returnUrl = $request->getUri();
            session(['returnUrl' => $returnUrl]);
            return redirect('member/login');
        }
        return $next($request);
    }
}
