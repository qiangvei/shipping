<?php

namespace file;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Read
{
    /**读取Excel或csv文件
     * @param string $file 文件路径
     * @return array|string 成功返回array ，失败返回string
     */
    public static function read($file=''){
        $data = [];
        if(!file_exists($file)){ return '文件不存在：'.$file;}
        try{
            $obj =IOFactory::load($file);
            $sheet = $obj->getActiveSheet();
            $rowsNum = $sheet->getHighestRow();
            $colNum = Coordinate::columnIndexFromString($sheet->getHighestColumn());
            for ($i=0;$i<$rowsNum;$i++){
                for ($c = 0;$c<$colNum;$c++){
                    $data[$i][$c] = $sheet->getCell(Coordinate::stringFromColumnIndex($c + 1) . ($i + 1))->getValue();
                    if (is_object($data[$i][$c])) {
                        $data[$i][$c] = $data[$i][$c]->__toString();
                    }
                    $data[$i][$c] = $data[$i][$c]==null?'':$data[$i][$c];
                }
            }

        }catch (Exception $e){
            return '异常：'.$e->getMessage();
        }
        return $data;
    }
}