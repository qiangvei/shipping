<?php
namespace app\controller;

use app\BaseController;
use common\CountryCode;
use file\Read;
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
//        $f = 'system/country_code.xlsx';
//        $data = Read::read($f);
        $obj = new CountryCode();

        dump($obj->getCode2ByChineseName('罗马尼亚'));
        dump($obj->getCode3ByChineseName('美国'));
        dump($obj->getChineseNameByCode('jp'));
        dump($obj);
        return 'hello';
    }

    public function import(){
        $req = $_FILES;
        return json($req);
    }
}
