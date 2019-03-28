<?php
namespace jext\wechat\wxpay;

use common\extend\jeen\JDebug;
use jext\wechat\core\WxBase;

class Client extends WxBase
{
    public function getMillisecond()
    {
        return (int) (microtime(true) * 1000);
    }

    /**
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * @param DataOrderQuery $input
     * @return mixed|DataResult
     * @throws \Exception
     */
    public function orderQuery(DataOrderQuery $input)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        $input->check();
        $input->appid = $this->appId;
        $input->mch_id = $this->mchId;

        $input->sign = $input->makeSign($this->signKey);//签名
        $xml = $input->toXml();
        $response = $this->postXmlCurl($xml, $url);
        $result = DataResult::handle($this, $response);
        return $result;
    }

    /**
     * 查询退款
     * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
     * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
     * WxPayRefundQuery中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param DataRefundQuery $input
     * @return DataResult
     * @throws \Exception
     */
    public function refundQuery(DataRefundQuery $input)
    {
        $url = "https://api.mch.weixin.qq.com/pay/refundquery";
        $input->check();
        $input->appid = $this->appId;
        $input->mch_id = $this->mchId;

        $input->sign = $input->makeSign($this->signKey);
        $xml = $input->toXml();

        $response = $this->postXmlCurl($xml, $url);
        $result = DataResult::handle($this, $response);
        return $result;
    }


    /**
     *
     * 关闭订单，WxPayCloseOrder中out_trade_no必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayCloseOrder $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public function closeOrder(DataCloseOrder $input)
    {
        $url = "https://api.mch.weixin.qq.com/pay/closeorder";
        $input->check();
        $input->appid = $this->appId;
        $input->mch_id = $this->mchId;

        $input->sign = $input->makeSign($this->signKey);
        $xml = $input->toXml();

        $response = $this->postXmlCurl($xml, $url);
        $result = DataResult::handle($this, $response);
        return $result;
    }


}