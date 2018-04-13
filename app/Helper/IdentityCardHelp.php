<?php
namespace App\Helper;

/**
 * 出入省份证号 返回相应信息
 * $result['status']     0：未知错误，1：身份证格式错误，2：无错误
 * $result['isAdult']      0标示成年，1标示未成年
 * $result['sex']       性别 男 女
 * $result['tdate']     生日，格式如：2012-11-15
 * Class IdentityCardHelp
 * @package App\Helper
 */

class IdentityCardHelp{

    static public function getIDCardInfo($IDCard){
        $result['status']=0;//0：未知错误，1：身份证格式错误，2：无错误
        $result['isAdult']='';//0标示成年，1标示未成年
        $result['sex'] ='';//性别 男 女
        $result['birthday']='';//生日，格式如：2012-11-15
        if(!preg_match("/^[1-9]([0-9a-zA-Z]{17}|[0-9a-zA-Z]{14})$/",$IDCard)){
            $result['status']=1;
            return $result;
        }

        switch(strlen($IDCard)){
            case 18:
                $tyear=intval(substr($IDCard,6,4));
                $tmonth=intval(substr($IDCard,10,2));
                $tday=intval(substr($IDCard,12,2));
                if($tyear>date("Y")||$tyear<(date("Y")-100)){
                    $flag=0;
                }elseif($tmonth<0||$tmonth>12){
                    $flag=0;
                }elseif($tday<0||$tday>31){
                    $flag=0;
                }else{
                    $tdate=$tyear."-".$tmonth."-".$tday;
                    if((time()-mktime(0,0,0,$tmonth,$tday,$tyear))>18*365*24*60*60){
                        $flag=0;
                    }else{
                        $flag=1;
                    }
                }
                $sex = 1;
                $sex_num = mb_substr($IDCard, 16, 1);
                if($sex_num % 2 == 0)
                    $sex = 0;
                break;
            case 15:
                $tyear=intval("19".substr($IDCard,6,2));
                $tmonth=intval(substr($IDCard,8,2));
                $tday=intval(substr($IDCard,10,2));
                if($tyear>date("Y")||$tyear<(date("Y")-100)){
                    $flag=0;
                }elseif($tmonth<0||$tmonth>12){
                    $flag=0;
                }elseif($tday<0||$tday>31){
                    $flag=0;
                }else{
                    $tdate=$tyear."-".$tmonth."-".$tday;
                    if((time()-mktime(0,0,0,$tmonth,$tday,$tyear))>18*365*24*60*60){
                        $flag=0;
                    }else{
                        $flag=1;
                    }
                }
                $sex = 1;
                $sex_num = mb_substr($IDCard, 14, 1);
                if($sex_num % 2 == 0)
                    $sex = 0;
                break;
        }

        $result['status']=2;//0：未知错误，1：身份证格式错误，2：无错误
        $result['isAdult']=$flag;//0标示成年，1标示未成年
        $result['sex'] = $sex; //性别 1男 0女
        $result['birthday']=$tdate;//生日日期
        return $result;
    }

}



