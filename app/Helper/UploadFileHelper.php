<?php
namespace App\Helper;

use Intervention\Image\ImageManagerStatic as Image;

class UploadFileHelper
{

    static public function uploadImages($files, $path, $type=['jpg', 'jpeg', 'png']){
        if(count($files) < 1)
            throw  new \Exception('没有被上传的图片');
        $image_path = array();
        foreach($files as $k => $v){
            $extension = $v->getClientOriginalExtension();
            if(!in_array($extension, $type)){
                throw  new \Exception('文件类型错误');
            }
            //创建目录777权限
            $arr = explode('/', $path);
            $aimDir = '';
            foreach ($arr as $str)
            {
                $aimDir .= $str . '/';
                if (!file_exists(public_path($aimDir))) {
                    mkdir($aimDir);
                    chmod($aimDir, 0777);
                }
            }
            $name = date("YmdHis") . rand(1000, 9999) . '.' . $extension;
            $v->move($path, $name);
            $image_path[] = $path . $name;
        }
        return $image_path;
    }


    static public function uploadImage($file, $path, $type=['jpg', 'jpeg', 'png']){
        if(!$file)
            throw  new \Exception('没有被上传的图片');
        $extension = $file->getClientOriginalExtension();
        if(!in_array($extension, $type)){
            throw  new \Exception('文件类型错误');
        }
        $size = filesize($file);
        $maxsize = 1048576;
        //创建目录777权限
        $arr = explode('/', $path);
        $aimDir = '';
        foreach ($arr as $str)
        {
            $aimDir .= $str . '/';
            if (!file_exists(public_path($aimDir))) {
                mkdir($aimDir);
                chmod($aimDir, 0777);
            }
        }
        $name = date("YmdHis") . rand(1000, 9999) . '.' . $extension;
        $file->move($path, $name);
        $image_path = $path . $name;
        if ($size > $maxsize) {
            $img = Image::make($image_path)->resize(1500, null, function($constraint){
                // 调整图像的宽到1000，并约束宽高比(高自动)
                $constraint->aspectRatio();
            });
            $img->save($image_path);
        }
        return $image_path;
    }

    static public function uploadFile($file,$path,$type=['xlsx','xls','xlsm','xltx','xltm','xlsb','xlam'])
    {
        if(!$file)
            throw  new \Exception('没有被上传的文件');
        $extension = $file->getClientOriginalExtension();
        if(!in_array($extension, $type)){
            throw  new \Exception('文件类型错误');
        }
        //创建目录777权限
        $arr = explode('/', $path);
        $aimDir = '';
        foreach ($arr as $str)
        {
            $aimDir .= $str . '/';
            if (!file_exists(public_path($aimDir))) {
                mkdir($aimDir);
                chmod($aimDir, 0777);
            }
        }
        $name = date("YmdHis") . rand(1000, 9999) . '.' . $extension;
        $file->move($path, $name);
        $image_path = $path . $name;
        return $image_path;
    }

}