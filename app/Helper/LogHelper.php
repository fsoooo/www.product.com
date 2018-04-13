<?php
namespace App\Helper;

use Carbon\Carbon;

class LogHelper{
    //错误回调日志
    static public function logError($data, $error_msg, $from=null, $type=null)
    {
        $log = "[error] [" . $from . '] [' .$type . "] [" . Carbon::now() . "] \n" . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
        $log .= "Error Message: " . $error_msg . "\n";
        $date = date('Y_m_d');
        $file_path = storage_path('logs/api_error_'. $date .'.log');
        file_put_contents($file_path, $log, FILE_APPEND);
    }

    //成功回掉日志
    static public function logSuccess($data, $from=null, $type=null)
    {
        $log = "[ SUCCESS ] [" . $from . '] [' .$type . "] [" . Carbon::now() . "] \n" . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
        $date = date('Y_m_d');
        $file_path = storage_path('logs/api_success_'. $date .'.log');
        file_put_contents($file_path, $log, FILE_APPEND);
    }

    static public function logOriginalError($data, $error_msg, $from=null, $type=null)
    {
        $log = "[error] [" . $from . '] [' .$type . "] [" . Carbon::now() . "] \n" . $data . "\n";
        $log .= "Error Message: " . $error_msg . "\n";
        $date = date('Y_m_d');
        $file_path = storage_path('logs/api_error_'. $date .'.log');
        file_put_contents($file_path, $log, FILE_APPEND);
    }
}









