<?php
namespace jext\wechat\wxpay;

/**
 * @see https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_5
 * Class DataRefundQuery
 * @package jext\wechat\wxpay
 */
class DataRefundQuery extends DataBase
{
    public $appid;
    public $mch_id;
    public $nonce_str;
    public $sign;
    public $sign_type = 'MD5';
    public $transaction_id;
    public $out_trade_no;
    public $out_refund_no;
    public $refund_id;
    public $offset;

    /**
     * @throws \Exception
     */
    public function check()
    {
        //检测必填参数
        if(!$this->transaction_id &&
            !$this->out_trade_no &&
            !$this->out_refund_no &&
            !$this->refund_id) {
            throw new \Exception("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！");
        }
    }

}