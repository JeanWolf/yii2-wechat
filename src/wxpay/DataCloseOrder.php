<?php
namespace jext\wechat\wxpay;

/**
 * @see https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_3
 * Class DataOrderQuery
 * @package jext\wechat\wxpay
 */
class DataCloseOrder extends DataBase
{
    public $appid;
    public $mch_id;
    public $out_trade_no;
    public $nonce_str;
    public $sign;
    public $sign_type = 'MD5';

    /**
     * @throws \Exception
     */
    public function check()
    {
        //检测必填参数
        if(!$this->out_trade_no) {
            throw new \Exception("订单查询接口中，out_trade_no必填！");
        }
    }

}