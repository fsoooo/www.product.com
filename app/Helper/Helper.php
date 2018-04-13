<?php
namespace App\Helper;

class Helper{

    /**
     * 获取所有子类
     * @param $data         待分类的数据
     * @param int $id       自身ID
     * @return array
     */
    static public function getSubTree(&$data , $id = 0) {
        static $son = array();

        foreach($data as $key => $value) {
            if($value['pid'] == $id) {
                $son[] = $value;
                unset($data[$key]);
                self::getSubTree($data , $value['id']);
            }
        }

        return $son;
    }

    /**
     * 获取所有父类
     * @param $data     待分类的数据
     * @param $pid      要找的祖先节点ID
     * @return array
     */
    static public function Ancestry($data , $pid) {
        static $ancestry = array();

        foreach($data as $key => $value) {
            if($value['id'] == $pid) {
                $ancestry[] = $value;
                self::Ancestry($data , $value['parent_id']);
            }
        }
        return $ancestry;
    }
}