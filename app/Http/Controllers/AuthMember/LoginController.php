<?php

namespace App\Http\Controllers\AuthMember;

use App\Model\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{

    //验证
    protected function validateMember(Request $request)
    {
        $this->validate($request, [
            'email' => 'required', 'password' => 'required',
        ]);
    }

    /**
     * 注册
     */
    public function register(Request $request)
    {
        //get请求 显示视图
        if ($request->isMethod('get')) {
            return view('auth-member.register');
        }

        $this->validateMember($request);

        $member = new Member();
        $member->name = $request->get('name');
        $member->email = $request->get('email');
        $member->password = bcrypt($request->get('password'));
        if ($member->save()) {
            //注册成功直接将该用户登陆 并且维持session跳转
            auth()->guard('member')->login($member);
            $returnUrl = session('returnUrl', '/');
            return redirect($returnUrl);
        }
    }

    /**
     * 登录
     */
    public function login(Request $request)
    {
        //get 显示视图
        if ($request->isMethod('get')) {
            return view('auth-member.login');
        }

        $this->validateMember($request);
        $data = $request->only('email', 'password');
        $bool = auth()->guard('member')->attempt($data);
        if ($bool) {
            //如果登录成功
            $returnUrl = session('returnUrl', '/');
            return redirect($returnUrl);
        } else {
            //abort('401','用户名或密码错误!');
            return redirect('member/login');
        }

    }

    /**
     * 退出
     */
    public function logout(Request $request)
    {
        auth()->guard('member')->logout();
        $request->session()->forget(auth()->guard('member')->getName());
        $request->session()->regenerate();
        return redirect('member/login');
    }

    //ajax、 验证邮箱登录
    public function checkEmail(Request $request)
    {
        $email = $request->get('email');
        $emails = DB::table('members')->pluck('email')->toArray();
        if (!in_array($email, $emails)) {
            return $res = 'No';
        }
        return true;
    }


}
