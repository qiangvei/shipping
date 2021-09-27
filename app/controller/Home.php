<?php
declare (strict_types = 1);

namespace app\controller;

use think\facade\View;

class Home
{
    public function index()
    {
        View::assign('right',session('user')?1:0);
        return View::fetch();
    }
}
