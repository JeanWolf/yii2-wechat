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

    public static function getKeyPath()
    {
        $path = dirname(__DIR__) . '/wxpay/cert/'.self::getMchId().'/apiclient_key.pem';
        return $path;
    }

    public static function getCertPath()
    {
        $path = dirname(__DIR__) . '/wxpay/cert/'.self::getMchId().'/apiclient_cert.pem';
        return $path;
    }


}
