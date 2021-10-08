<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\SpShippingFee;
use file\Read;
use think\facade\Db;
use think\facade\Log;
use think\Request;

class ShippingQuotedPrice
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    public function getData(){
        $page =  (int)\request()->get('page',1);
        $limit = (int)\request()->get('limit',15);
        $field = \request()->get('field','pk');
        $order = \request()->get('order','asc');
        $keyword = \request()->get('keyword','');
        $weight = \request()->get('weight','');

        $obj = Db::table('Sp_Shipping_Fee');
        if(trim($keyword)!=''){
            $obj->whereRaw("(dest_country_id='".$keyword."' or dest_region_id='".$keyword."' or  dest_zip='".$keyword."')");
        }
        if((float)$weight>0){
            $obj->where('weight','>=',$weight)
                ->group('dest_country_id , dest_region_id , dest_zip , carrier_code , carrier_title , method_code , method_title')
                ->fieldRaw('pk,dest_country_id , dest_region_id , dest_zip ,  min(weight) as weight  ,  min(price) as price , carrier_code , carrier_title , method_code , method_title,addtime');
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
                $d['dest_region_id'] = trim($data[$i][1]);
                $d['dest_zip'] = trim($data[$i][2]);
                $d['weight'] = (float)$data[$i][3];
                $d['price'] = (float)$data[$i][4];
                $d['carrier_code'] = trim($data[$i][5]);
                $d['carrier_title'] = trim($data[$i][6]);
                $d['method_code'] = trim($data[$i][7]);
                $d['method_title'] = trim($data[$i][8]);
                $insertData[] = $d;
                $d = [];
            }
            if($insertData){
                $rows = Db::name('sp_shipping_fee')->replace()->insertAll($insertData);
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
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
