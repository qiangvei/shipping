<?php
declare (strict_types = 1);

namespace app\controller;

use think\facade\Request;
use think\facade\View;

class User
{
    public function login()
    {
        return View::fetch();
    }
    public function logindo(){
        $data = [
            'code' => 1,
            'msg'  => '登陆信息错误！',
        ];
        $username = Request::param('username');
        $password = Request::param('password');
        $finduser = \app\model\User::getUser($username);

        if($finduser){
            if(md5($finduser['password']) === md5($password)){
                $data['code'] = 0;
                $data['msg'] = '登陆成功';
                session('user',$finduser);
            }
        }
        return json($data);
    }
    public function loginout(){
        session('user',null);
        return 1;
    }

}
