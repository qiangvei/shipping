<?php
declare (strict_types = 1);

namespace app\controller;

use think\facade\View;

class Home
{
    public function index()
    {
        return View::fetch();
    }
}
