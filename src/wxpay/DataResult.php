<?php
namespace jext\wechat\wxpay;

class DataResult extends DataBase
{
    public $return_code; //返回状态码 SUCCESS/FAIL
    public $return_msg; //返回信息 ...
    public $appid; //微信应用ID
    public $mch_id; //商户号
    public $nonce_str; //随机字符串
    public $sign; // 签名
    public $result_code; // 业务结果  SUCCESS/FAIL
    public $err_code;  //错误代码
    public $err_code_des; //错误代码描述
    public $device_info; //设备号
    public $openid; //用户标识
    public $is_subscribe; //是否关注公众帐号
    public $trade_type; //交易类型  JSAPI，NATIVE，APP，MICROPAY
    /** 交易状态
     * SUCCESS—支付成功
     * REFUND—转入退款
     * NOTPAY—未支付
     * CLOSED—已关闭
     * REVOKED—已撤销（刷卡支付）
     * USERPAYING--用户支付中
     * PAYERROR--支付失败(其他原因，如银行返回失败)
     * @var @trade_state
     */
    public $trade_state; // 交易状态
    public $bank_type; //付款银行
    public $total_fee; //标价金额  订单总金额，单位为分
    public $settlement_total_fee; //应结订单金额
    public $fee_type; // 标价币种  默认CNY
    public $cash_fee; // 现金支付金额
    public $cash_fee_type = 'CNY'; //现金支付币种
    public $coupon_fee; //代金券金额
    public $coupon_count; //代金券使用数量
    public $transaction_id; //微信支付单号
    public $out_trade_no; //商户单号
    public $attach; //附加数据  原样返回
    public $time_end; //支付完成时间
    public $trade_state_desc; //交易状态描述

    public $total_refund_count; //订单总退款次数  传入 offset后有返回
    public $refund_count; //退款笔数   当前返回
    public $refund_fee; //退款金额   当前返回
    public $out_refund_no_x; //diy 商户退款单号
    public $refund_id_x; //diy 微信退款单号
    public $refund_channel_x; //diy 退款渠道
    public $refund_fee_x; //diy 申请退款金额
    public $settlement_refund_fee_x; //diy 退款金额
    public $coupon_type_x_y; //diy 代金券类型
    public $coupon_refund_fee_x; //diy 总代金券退款金额
    public $coupon_refund_count_x; //diy 退款代金券使用数量
    public $coupon_refund_id_x_y; //diy 退款代金券ID
    public $coupon_refund_fee_x_y;//diy 单个代金券退款金额
    public $refund_status_x; //diy 退款状态
    public $refund_account_x; //diy 退款资金来源
    public $refund_recv_account_x; //diy 退款入账账户
    public $refund_success_time_x;//diy 退款成功时间

    public $info;//all response data info

    public function makeSign($signKey)
    {
        $attrs = $this->info;
        //签名步骤一：按字典序排序参数
        ksort($attrs);
        $string = $this->toUrlParams($attrs);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$signKey;
        //签名步骤三：MD5加密或者HMAC-SHA256
        if(strlen($this->sign) <= 32){
            //如果签名小于等于32个,则使用md5验证
            $string = md5($string);
        } else {
            //是用sha256校验
            $string = hash_hmac("sha256", $string, $signKey);
        }
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    public function checkSign($signKey)
    {
        if(!$this->sign){
            throw new \Exception("签名错误！");
        }

        $sign = $this->makeSign($signKey);
        if($this->sign == $sign){
            //签名正确
            return true;
        }
        throw new \Exception("签名错误！");
    }

    /**
     * @param Client $client
     * @param $xml
     * @return DataResult
     * @throws \Exception
     */
    public static function handle(Client $client, $xml)
    {
        $obj = new self();
        $obj->info = $obj->xmlToArr($xml);
        $obj->setAttributes($obj->info, false);
        //失败则直接返回失败
        if($obj->return_code != 'SUCCESS') {
            foreach ($obj->getAttributes() as $key => $value) {
                #除了return_code和return_msg之外其他的参数存在，则报错
                if($key != "return_code" && $key != "return_msg"){
                    throw new \Exception("输入数据存在异常！");
                }
            }
            return $obj;
        }
        $obj->checkSign($client->signKey);
        return $obj;
    }

}