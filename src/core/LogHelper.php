<?php
namespace jext\wechat\core;

use jext\jeen\JLog;

class LogHelper
{
    public static function log($msg,$dir='')
    {
        return JLog::log($msg, 'jtx/log/'.$dir);
    }

    public static function exception($e, $data=[],$dir='')
    {
        return JLog::exception($e, $data, 'jtx/'.$dir);
    }

}
