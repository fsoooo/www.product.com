<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * 上传图片公共类
 *
 * @category   Service
 * @package    Api
 * @author     房玉婷 <fangyt@inschos.com>
 * @copyright  2017 (C) 北京天眼互联科技有限公司
 * @version    1.0.0
 * @since      v1.0
 */
class UploadImage
{
    /**
     * constructer.
     */
    public function __construct()
    {
    }

    /**
     * 把base64转换成image并上传到服务器
     *
     * @param $base64
     * @param $path
     * @return bool
     */
    public static function uploadImageWithBase($base64, $path)
    {
        $base64_string = strchr($base64, 'base64,');
        //base64的图片
        $base64_string = substr($base64_string, 7, strlen($base64_string) - 7);

        //后缀 png|jpeg|PNG|JPEG|jpg|JPG
        if (is_int(strpos($base64, 'png')) || is_int(strpos($base64, 'PNG'))) {
            $suffix = '.png';
        } elseif (is_int(strpos($base64, 'jpg')) || is_int(strpos($base64, 'jpeg')) || is_int(strpos($base64, 'JPG'))) {
            $suffix = '.jpg';
        } else {
            //后缀名不对
            return false;
        }
        $output_file = time() . rand(100, 999) . $suffix;

        $base64_decode = preg_replace("/\s/", '+', $base64_string);
        Storage::disk('upload')->put($path . $output_file, base64_decode($base64_decode));

        return $output_file;
    }
}
