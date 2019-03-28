<?php

namespace jext\wechat\core;

use Yii;
use yii\base\Component;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

class WxBase extends Component
{
    public $appId = '';
    public $appSecret = '';

    public $mchId = '';
    public $signKey = '';

    public $body = '';
    public $notifyUrl = '';
    public $refundNotifyUrl = '';

    /** @var Client $client */
    public $client;
    /** @var Request $request */
    public $request;
    /** @var Response $response */
    public $response;

    public $baseUrl = 'https://api.weixin.qq.com/';
    public $apiName;
    public $requestUrl; //url string, default make by baseUrl . apiName
    public $headers; // headers array
    public $options; // curl options array
    public $getParams; //get params array
    public $postParams; //post params array
    public $paramType; // form | json


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
        $this->resetRequest();
        $this->apiName = 'cgi-bin/token';
        $this->getParams = [
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'grant_type' => 'client_credential',
        ];

        $res = Json::decode($this->sendRequest());
        if (isset($res['access_token'])) {
            $cache->set($key, $res['access_token'], $res['expires_in'] - 180);
            return $res['access_token'];
        }
        return '';
    }

    public function resetRequest()
    {
        $this->client = new Client();
        $this->client->setTransport(CurlTransport::class);
        $this->request = new Request(['client' => $this->client]);
        $this->headers = [];
        $this->options = [
            'timeout' => 3,
            'maxRedirects' => 3
        ];
        $this->baseUrl='https://api.weixin.qq.com/';
        $this->apiName = '';
        $this->requestUrl = '';
        $this->getParams = [];
        $this->postParams = [];
        $this->paramType = 'form';
    }

    /**
     * 与 resetRequest 配套使用
     * @return bool|string
     */
    public function sendRequest()
    {
        if (!$this->apiName && !$this->requestUrl) {
            return false;
        }

        $this->requestUrl = $this->requestUrl ? : $this->baseUrl . $this->apiName;
        if ($this->options) {
            $this->request->addOptions($this->options);
        }
        if ($this->headers) {
            $this->request->addHeaders($this->headers);
        }
        if ($this->getParams) {
            if (strpos($this->requestUrl,'?')) {
                $this->requestUrl .= '&'.http_build_query($this->getParams);
            } else {
                $this->requestUrl .= '?'.http_build_query($this->getParams);
            }
        }
        if (!in_array($this->request->getMethod(), ['get','GET']) || $this->postParams) {
            if ($this->paramType == 'json') {
                $this->request->addHeaders(['Content-Type' => 'application/json']);
                $this->request->setContent(json_encode($this->postParams));
            } else {
                $this->request->setContent(http_build_query($this->postParams));
            }
        }

        $this->request->setUrl($this->requestUrl);

        try {
            $uniqueId = md5($this->appId.':'.microtime(true).':'.$this->requestUrl);
            LogHelper::log([
                $uniqueId .' [RequestStart] '.get_called_class(),
                'requestUrl' => $this->requestUrl,
                'getParams' => $this->getParams,
                'postParams' => $this->postParams,
            ], 'request');
            $this->response = $this->client->send($this->request);
            LogHelper::log($uniqueId . ' [RequestDone] ' . $this->response->getContent(), 'request');
        } catch (\Exception $e) {
            LogHelper::exception($e, [
                $this->requestUrl,
                $this->paramType,
                $this->postParams,
            ]);
            $this->response = new Response();
        }

        return $this->response->getContent();
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

    public function postXmlCurl($xml, $url, $useCert = false, $second = 3)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

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
            return $data;
        } else {
            $code = curl_errno($ch);
            $msg = curl_error($ch);
            curl_close($ch);
            throw new \Exception("curl出错,Code:$code,Msg:$msg");
        }
    }

    public function httpGet($url, $safe = true) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);

        if ($safe) {
            // 为保证数据传输的安全性，采用https方式调用，必须使用下面2行代码打开ssl安全校验。
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }

        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    public function httpPost($url, $body, $type='json', $safe = true)
    {
        if (!is_string($body)) {
            if ($type == 'json') {
                $body = json_encode($body);
            } else {
                $body = http_build_query($body);
            }
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);

        if ($safe) {
            // 为保证数据传输的安全性，采用https方式调用，必须使用下面2行代码打开ssl安全校验。
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }

        if ($type == 'json') {
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Connection: keep-alive",
                "Content-Type: application/json; charset=UTF-8", //传送的数据类型
                "Content-Length: ".strlen($body) //传送数据长度
            ]);
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Connection: keep-alive"
            ]);
        }
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);//要传送的所有数据

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }


}
