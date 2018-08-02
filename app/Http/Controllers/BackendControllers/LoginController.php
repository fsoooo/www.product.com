<?php

namespace App\Http\Controllers\BackendControllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Helper\CaptchaHelper;
use Illuminate\Support\Facades\Input;
use Captcha;

class LoginController extends BaseController
{
    //登陆页面展示
    public function index()
    {
        if(Auth::guard('admin')->check()){
            return redirect('/backend');
        }
        return view('backend.login.login');
    }

    //执行登陆
    public function login(Request $request)
    {

//        //验证验证码
//        if(empty(CaptchaHelper::verifyCaptcha(Input::get('captcha')))) return back()->withErrors('验证码错误！');

        //包提供的验证
        if(empty(Captcha::check(Input::get('captcha')))) return back()->withErrors('验证码错误！');

        if(Auth::guard('admin')->check()){
            return redirect('/backend');
        }
        $email = $this->request->email;
        $password = $this->request->password;

        //验证登陆
        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $password])) {
            return redirect('/backend');
        } else {
            return back()->withErrors('账户或密码错误！');
        }
    }

    //退出登陆
    public function logout()
    {
        if(Auth::guard('admin')->user())
            \Cache::forget('user_roles_array' . Auth::guard('admin')->user()->email);
        Auth::guard('admin')->logout();
        return redirect('backend/login');
    }

}