<?php
namespace app\controller;

use app\BaseController;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        View::assign('user',session('user'));
        return View::fetch();
    }
    public function test(){
        return json(['200H','300H']);
    }

    public function hello(){
        return 'hello';
    }

    public function import(){
        $req = $_FILES;
        return json($req);
    }
}
