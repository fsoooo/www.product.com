<?php
/**
 *
 * Author: mingyang <7789246@qq.com>
 * Date: 2017-12-26
 */

namespace App\Helper;

use Captcha;
use Validator;

class CaptchaHelper
{
    //验证验证码
    static public function verifyCaptcha($captcha){

        if (Captcha::check($captcha)) {
            return true;
        }else{
            return false;
        }
    }
}