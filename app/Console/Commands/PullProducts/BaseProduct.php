<?php

namespace App\Console\Commands\PullProducts;
/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2018/2/6
 * Time: 17:43
 */
interface BaseProduct
{
    /**
     * 初始化链接参数
     * @return mixed
     */
    public function __construct();

    /**
     * 获取产品信息
     * @param $p_code_array
     * @return mixed
     */
    public function pullProducts($p_code_array);

    /**
     * @param string $p_code
     * @return mixed
     */
    public function pulling($p_code);

    /**
     * 格式化返回
     * @param $data
     * @return mixed
     */
    public function formatReturn($data);
}