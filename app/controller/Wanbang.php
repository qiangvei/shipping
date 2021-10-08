<?php
declare (strict_types = 1);

namespace app\controller;

use file\Read;
use think\facade\Db;
use think\facade\View;

class Wanbang
{
    public function index()
    {
        View::assign('right',session('user')?1:0);
        return View::fetch();

    }
    public function getData(){
        $page =  (int)\request()->get('page',1);
        $limit = (int)\request()->get('limit',15);
        $field = \request()->get('field','pk');
        $order = \request()->get('order','asc');
        $keyword = \request()->get('keyword','');
        $weight = \request()->get('weight','');

        $obj = Db::table('sp_shipping_wanbangfee');
        if(trim($keyword)!=''){
            $obj->whereRaw("(dest_country_id='".$keyword."' or method_title_cn='".$keyword."' or  carrier_title='".$keyword."')");
        }
        if((float)$weight>0){
            $obj->where('weight_min','<',$weight)
                ->where('weight_max','>=',$weight);
        }

        $count = $obj->count();
        $data = $obj->page($page,$limit)->order($field,$order)->select()->toArray();

        for($i=0;$i<count($data);$i++){
            $data[$i]['right'] = 0;
        }
        return json(['code'=>0,'msg'=>'完成','count'=>$count,'data'=>$data,'search'=>['keyword'=>$keyword,'weight'=>$weight]]);
    }

    public function upload()
    {
        $res = [
            'code' => 1,
            'msg' => '未读取',
            'data' => ''
        ];
        $file = request()->file('importfile');
        $data = Read::read($file->getRealPath());
        if(is_array($data)){
            $insertData = [];
            for($i=1;$i<count($data);$i++){
                if(count($data[$i])<9){continue;}
                $d['dest_country_id'] = trim($data[$i][0]);
                $d['weight_min'] = (float)$data[$i][1];
                $d['weight_max'] = (float)$data[$i][2];
                $d['unit_price'] = (float)$data[$i][3];
                $d['handling_fee'] = (float)$data[$i][4];
                $d['carrier_code'] = trim($data[$i][5]);
                $d['carrier_title'] = trim($data[$i][6]);
                $d['method_code'] = trim($data[$i][7]);
                $d['method_title'] = trim($data[$i][8]);
                $d['method_title_cn'] = $data[$i][9]??'';
                $d['max_length'] = (int)$data[$i][10]??9999;
                $d['max_width'] = (int)$data[$i][11]??9999;
                $d['max_height'] = (int)$data[$i][12]??9999;
                $insertData[] = $d;
                $d = [];
            }
            if($insertData){
                $rows = Db::name('sp_shipping_wanbangfee')->replace()->insertAll($insertData);
                if($rows>0){
                    $res['code'] = 0;
                    $res['msg'] = '成功';
                }else{
                    $res['msg'] = '数据写入失败';
                }
            }
            $res['data'] = $insertData;
        }
        return json($res);
    }

    /**
     * @param string $countrycode 国家代码
     * @param int $weight 包裹重量
     * @param int $len  包裹：长 。默认：-1 不筛选
     * @param int $width 包裹：宽 。默认：-1 不筛选
     * @param int $height 包裹：高 。默认：-1 不筛选
     * @return array
     */
    public static function getQuotes($countrycode='',$weight=0,$len=-1,$width=-1,$height=-1){
        $data = [];
        if(empty($countrycode)){return $data;}
        if((float)$weight<=0){ return $data;}
        $obj = Db::table('sp_shipping_wanbangfee')
            ->where('dest_country_id','=',$countrycode)
            ->where('weight_min','<',$weight)
            ->where('weight_max','>=',$weight);
        if((int)$len>0){
            $obj->where('max_length','>=',$len);
        }
        if((int)$width>0){
            $obj->where('max_width','>=',$width);
        }
        if((int)$height>0){
            $obj->where('max_height','>=',$height);
        }

        $res = $obj->select()->toArray();
        for ($i=0;$i<count($res);$i++){
            $data[] = [
                'carrier_code'=> $res[$i]['carrier_code'],
                'carrier_title' => $res[$i]['carrier_title'],
                'method_code' => $res[$i]['method_code'],
                'method_title' => $res[$i]['method_title'],
                'price' => (float)$res[$i]['unit_price'] * (float)$weight + (float)$res[$i]['handling_fee'],
            ];
        }
        return $data;
    }
}
