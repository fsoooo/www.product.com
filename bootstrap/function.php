<?php
/**
 * Created by PhpStorm.
 * User: dell01
 * Date: 2017/5/4
 * Time: 11:38
 */

function returnJson($status,$data){
    $result['status'] = $status;
    $result['data'] = $data;
    return json_encode($result,true);
}