<?php
namespace app\controller;

use app\BaseController;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        return View::fetch();
    }
    public function home(){
        return 'home';
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
