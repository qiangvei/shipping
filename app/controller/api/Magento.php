<?php
declare (strict_types = 1);

namespace app\controller\api;

use app\model\SpShippingFee;
use think\facade\Config;
use think\facade\Db;
use think\facade\Log;
use think\facade\Request;

class Magento
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return 'api.Magento';
    }

    public function read()
    {
        $res = [];

        $data = Request::get('data');
        if(!$data){return json_encode($res);}
        $data = json_decode($data,true);
        $token = Request::get('token');
        $time = Request::get('time');
        Log::debug($data);
        $verify = $this->verify($token,$time);
        if($verify){
            $weight = $data['PackageWeight']??0;
            $weightUnit = $data['WeightUnit']??'kgs';
            switch (strtolower($weightUnit)){
                case 'kg':
                    break;
                case 'kgs':
                    break;
                case 'lbs':
                    $weight = bcdiv($weight , '2.204623',3);
                    break;
                default:
                    break;
            }
            $DestCountryId = $data['DestCountryId']??'*';
            $DestRegionId = $data['DestRegionId']??'*';
            $DestPostcode = $data['DestPostcode']??'*';
            $dd = Db::table('Sp_Shipping_Fee')
//                ->where([
//                'dest_country_id'=>$DestCountryId,
//                'dest_region_id' => $DestRegionId,
//                'dest_zip' => $DestPostcode
//            ])
                ->whereRaw("(dest_country_id='*' or dest_country_id='".$DestCountryId."')")
                ->where('weight','>=',$weight)
                ->group('dest_country_id , dest_region_id , dest_zip , carrier_code , carrier_title , method_code , method_title')
                ->fieldRaw('min(price) as price , carrier_code , carrier_title , method_code , method_title')
                ->select();
            for($i=0;$i<count($dd);$i++){
                $res[] = [
                    'CarrierCode'=> $dd[$i]['carrier_code'],
                    'CarrierTitle' => $dd[$i]['carrier_title'],
                    'MethodCode' => $dd[$i]['method_code'],
                    'MethodTitle' => $dd[$i]['method_title'],
                    'Price' => $dd[$i]['price'],
                ];
            }
        }

        return json_encode($res);
    }

    private function verify($token=null,$time=null){
        if (!$token){ return false;}
        if(!$time){return false;}
        $username = Config::get('magento.user.username','');
        $password = Config::get('magento.user.password','');
        $vtoken = md5(md5($username.$password.$time).$time);
        return $token==$vtoken;
    }

    public function dd(){

        return json(['a'=>1,'b'=>2]);
        $username = Config::get('magento.user.username','');
        $password = Config::get('magento.user.password','');
        $time = time();
        $vtoken = md5(md5($username.$password.$time).$time);
        return json([$time,$vtoken]);
    }
}
