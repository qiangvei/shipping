<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class User extends Model
{
    //
    public static function getUser($name=''){
        $users = [
            [
                'id' => '1001' ,
                'username' => 'admin' ,
                'password' => 'kltadmin2503'
            ],
        ];
        if(!$name){
            return false;
        }
        $u = false;
        foreach ($users as $v){
            if($name === $v['username']){
                $u = $v;
                break;
            }
        }
        return $u;
    }
}
