<?php

namespace jext\wechat\core;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

class WxBase extends Component
{
    public $appId = '';
    public $appSecret = '';

    public $mchId = '';
    public $signKey = '';

    public $body = '';
    public $notifyUrl = '';
    public $refundNotifyUrl = '';

    public $baseUrl = 'https://api.weixin.qq.com/';

    public function init()
    {
        parent::init();
        if (!$this->appSecret) {
            $this->initConfig($this->appId);
        }
    }

    public function initConfig($appId)
    {
        if (!$appId || !isset(Yii::$app->params['wx'][$appId])) {
            throw new \Exception("wx config for [$appId] not found");
        }
        $conf = Yii::$app->params['wx'][$appId];
        $this->appSecret = $conf['appSecret'] ?? '';

        $this->mchId = $conf['mchId'] ?? '';
        $this->signKey = $conf['signKey'] ?? '';

        $this->body = $conf['body'] ?? '';
        $this->notifyUrl = $conf['notifyUrl'] ?? '';
        $this->refundNotifyUrl = $conf['refundNotifyUrl'] ?? '';
    }

    public function getAccessToken($appId = null)
    {
        $appId = $appId ? : $this->appId;
        if (!$appId) return '';
        $cache = CacheHelper::getInstance();
        $key = "WX:AT:{$appId}";
        $at = $cache->get($key);
        if ($at) {
            return $at;
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/token?'
            . http_build_query([
                'appid' => $this->appId,
                'secret' => $this->appSecret,
                'grant_type' => 'client_credential',
            ]);

        try {
            $res = Json::decode($this->httpGet($url));
            $at = $res['access_token'] ?? '';
            if ($at) {
                $cache->set($key, $at, 7020);
                return $at;
            }
        } catch (\Exception $e) {
            //skip...
        }
        return '';
    }

    /**
     * 这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     */
    const CURL_PROXY_HOST = '0.0.0.0';
    const CURL_PROXY_PORT = 0;

    public function getKeyPath()
    {
        $path = dirname(__DIR__) . '/wxpay/cert/'.$this->mchId.'/apiclient_key.pem';
        return $path;
    }

    public function getCertPath()
    {
        $path = dirname(__DIR__) . '/wxpay/cert/'.$this->mchId.'/apiclient_cert.pem';
        return $path;
    }

    public function postXmlCurl($xml, $url, $useCert = false, $timeout = 3)
    {
        $_hash = md5($url.'|'.$xml.'|'.uniqid());
        LogHelper::log([
            'reqHash POST' => $_hash,
            'url' => $url,
            'body' => $xml,
        ]);
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_MAXREDIRS, 5);

        //如果有配置代理这里就设置代理
        if(self::CURL_PROXY_HOST != '0.0.0.0'
            && self::CURL_PROXY_PORT != 0){
            curl_setopt($ch,CURLOPT_PROXY, self::CURL_PROXY_HOST);
            curl_setopt($ch,CURLOPT_PROXYPORT, self::CURL_PROXY_PORT);
        }
        curl_setopt($ch,CURLOPT_URL, $url);
        if($useCert == true){
            //设置证书
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $this->getCertPath());
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $this->getKeyPath());
        } else {
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        }
        //设置header
//        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            LogHelper::log("resHash: {$_hash} | resContent: $data");
            return $data;
        } else {
            $code = curl_errno($ch);
            $msg = curl_error($ch);
            curl_close($ch);
            LogHelper::log("resHash: {$_hash} | errorCode: $code | Msg: $msg");
            throw new \Exception("curl出错,Code:$code,Msg:$msg");
        }
    }

    public function httpGet($url, $safe = true, $timeout = 3)
    {
        $_hash = md5($url.'|'.uniqid());
        LogHelper::log([
            'reqHash GET' => $_hash,
            'url' => $url,
        ]);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch,CURLOPT_MAXREDIRS, 5);

        if ($safe) {
            // 为保证数据传输的安全性，采用https方式调用，必须使用下面2行代码打开ssl安全校验。
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            LogHelper::log("resHash: {$_hash} | resContent: $data");
            return $data;
        } else {
            $code = curl_errno($ch);
            $msg = curl_error($ch);
            curl_close($ch);
            LogHelper::log("resHash: {$_hash} | errorCode: $code | Msg: $msg");
            throw new \Exception("curl出错,Code:$code,Msg:$msg");
        }
    }

    public function httpPost($url, $body, $type='json', $safe = true, $timeout = 3)
    {
        if (!is_string($body)) {
            if ($type == 'json') {
                $body = json_encode($body);
            } else {
                $body = http_build_query($body);
            }
        }
        $_hash = md5($url.'|'.$body.'|'.uniqid());
        LogHelper::log([
            'reqHash POST' => $_hash,
            'url' => $url,
            'body' => $body,
        ]);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch,CURLOPT_MAXREDIRS, 5);

        if ($safe) {
            // 为保证数据传输的安全性，采用https方式调用，必须使用下面2行代码打开ssl安全校验。
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }

        if ($type == 'json') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Connection: keep-alive",
                "Content-Type: application/json; charset=UTF-8", //传送的数据类型
                "Content-Length: ".strlen($body) //传送数据长度
            ]);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Connection: keep-alive"
            ]);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);//要传送的所有数据

        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            LogHelper::log("resHash: {$_hash} | resContent: $data");
            return $data;
        } else {
            $code = curl_errno($ch);
            $msg = curl_error($ch);
            curl_close($ch);
            LogHelper::log("resHash: {$_hash} | errorCode: $code | Msg: $msg");
            throw new \Exception("curl出错,Code:$code,Msg:$msg");
        }
    }


}
