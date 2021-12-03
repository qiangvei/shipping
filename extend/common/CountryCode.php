<?php
namespace common;

use file\Read;

class CountryCode
{
    /**
     * 国家代码数据源文件
     */
    const COUNTRY_FILE_PATH = 'system/country_code.xlsx';
    /**国家代码数据
     * @var array
     */
    private $_data = [];

    public function __construct()
    {
        $tmp = $this->loadData();
        $this->_data = is_array($tmp)?$tmp:[];
    }

    /**读取国家代码源文件
     * @return array|string
     */
    private function loadData(){
        $data = Read::read(self::COUNTRY_FILE_PATH);
        if(is_array($data) && !empty($data)){
            $tmp = [];
            foreach ($data as $v){
                $nameZH = $v[0]??'';
                $code2  = $v[1]??'';
                $code3  = $v[2]??'';
                $tmp[trim($code2)] = [
                    0 => trim($nameZH),
                    1 => trim($code2),
                    2 => trim($code3)
                ];
            }
            $data = $tmp;
        }
        return $data;
    }

    /**通过中文名获取二字码
     * @param string $cn_name
     * @return string
     */
    public function getCode2ByChineseName($cn_name=''){
        $data = '';
        if(empty($this->_data)){return $data;}
        $cn_name = trim($cn_name);
        foreach ($this->_data as $v){
            if($v[0]==$cn_name){
                $data = $v[1];
                break;
            }
        }
        return $data;
    }
    /**通过中文名获取三字码
     * @param string $cn_name
     * @return string
     */
    public function getCode3ByChineseName($cn_name=''){
        $data = '';
        if(empty($this->_data)){return $data;}
        foreach ($this->_data as $v){
            if($v[0]==$cn_name){
                $data = $v[2];
                break;
            }
        }
        return $data;
    }

    /**通过二字码或三字码获取中文名称
     * @param string $code
     * @return mixed|string
     */
    public function getChineseNameByCode($code=''){
        $data = '';
        if(empty($this->_data)){return $data;}
        $code = strtoupper($code);
        foreach ($this->_data as $v){
            if($v[1]==$code || $v[2] == $code){
                $data = $v[0];
                break;
            }
        }
        return $data;
    }
}