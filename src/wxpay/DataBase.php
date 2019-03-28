<?php
namespace jext\wechat\wxpay;

use yii\base\Model;

class DataBase extends Model
{
    public function toXml($params = null)
    {
        $attrs = $params ? : $this->getAttributes();
        if (!is_array($attrs) || count($attrs) <= 0) {
            throw new \Exception("数组数据异常！");
        }
        $xml = "<xml>";
        foreach ($attrs as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            } else {
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public function xmlToArr($xmlStr)
    {
        if (!$xmlStr) return [];
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $attrs = json_decode(json_encode(simplexml_load_string($xmlStr, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $attrs;
    }

    public function fromXml($xml)
    {
        if (!$xml) {
            throw new \Exception("xml数据异常！");
        }
        $attrs = $this->xmlToArr($xml);
        $this->setAttributes($attrs, false);
        return $this->getAttributes();
    }

    public function toUrlParams($params = null)
    {
        $buff = "";
        $attrs = is_null($params) ? $this->getAttributes() : $params;
        foreach ($attrs as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    public function makeSign($signKey)
    {
        $attrs = $this->getAttributes(null, ['sign']);
        //签名步骤一：按字典序排序参数
        ksort($attrs);
        $string = $this->toUrlParams($attrs);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$signKey;
        //签名步骤三：MD5加密或者HMAC-SHA256
        if($this->sign_type == "MD5"){
            $string = md5($string);
        } else if($this->sign_type == "HMAC-SHA256") {
            $string = hash_hmac("sha256", $string, $signKey);
        } else {
            throw new \Exception("签名类型不支持！");
        }

        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

}