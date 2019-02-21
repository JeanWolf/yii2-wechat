<?php
namespace jext\wechat\core;

class ErrorCode
{
    public static $codeList = [
        40001 => [
            'code' => 40001,
            'des_en' => 'invalid credential',
            'des_cn' => '不合法的调用凭证'
        ],
        40002 => [
            'code' => 40002,
            'des_en' => 'invalid grant_type',
            'des_cn' => '不合法的grant_type'
        ],
        40003 => [
            'code' => 40003,
            'des_en' => 'invalid openid',
            'des_cn' => '不合法的OpenID'
        ],
        40004 => [
            'code' => 40004,
            'des_en' => 'invalid media type',
            'des_cn' => '不合法的媒体文件类型'
        ],
        40007 => [
            'code' => 40007,
            'des_en' => 'invalid media_id',
            'des_cn' => '不合法的media_id'
        ],
        40008 => [
            'code' => 40008,
            'des_en' => 'invalid message type',
            'des_cn' => '不合法的message_type'
        ],
        40009 => [
            'code' => 40009,
            'des_en' => 'invalid image size',
            'des_cn' => '不合法的图片大小'
        ],
        40010 => [
            'code' => 40010,
            'des_en' => 'invalid voice size',
            'des_cn' => '不合法的语音大小'
        ],
        40011 => [
            'code' => 40011,
            'des_en' => 'invalid video size',
            'des_cn' => '不合法的视频大小'
        ],
        40012 => [
            'code' => 40012,
            'des_en' => 'invalid thumb size',
            'des_cn' => '不合法的缩略图大小'
        ],
        40013 => [
            'code' => 40013,
            'des_en' => 'invalid appid',
            'des_cn' => '不合法的AppID'
        ],
        40014 => [
            'code' => 40014,
            'des_en' => 'invalid access_token',
            'des_cn' => '不合法的access_token'
        ],
        40015 => [
            'code' => 40015,
            'des_en' => 'invalid menu type',
            'des_cn' => '不合法的菜单类型'
        ],
        40016 => [
            'code' => 40016,
            'des_en' => 'invalid button size',
            'des_cn' => '不合法的菜单按钮个数'
        ],
        40017 => [
            'code' => 40017,
            'des_en' => 'invalid button type',
            'des_cn' => '不合法的按钮类型'
        ],
        40018 => [
            'code' => 40018,
            'des_en' => 'invalid button name size',
            'des_cn' => '不合法的按钮名称长度'
        ],
        40019 => [
            'code' => 40019,
            'des_en' => 'invalid button key size',
            'des_cn' => '不合法的按钮KEY长度'
        ],
        40020 => [
            'code' => 40020,
            'des_en' => 'invalid button url size',
            'des_cn' => '不合法的url长度'
        ],
        40023 => [
            'code' => 40023,
            'des_en' => 'invalid sub button size',
            'des_cn' => '不合法的子菜单按钮个数'
        ],
        40024 => [
            'code' => 40024,
            'des_en' => 'invalid sub button type',
            'des_cn' => '不合法的子菜单类型'
        ],
        40025 => [
            'code' => 40025,
            'des_en' => 'invalid sub button name size',
            'des_cn' => '不合法的子菜单按钮名称长度'
        ],
        40026 => [
            'code' => 40026,
            'des_en' => 'invalid sub button key size',
            'des_cn' => '不合法的子菜单按钮KEY长度'
        ],
        40027 => [
            'code' => 40027,
            'des_en' => 'invalid sub button url size',
            'des_cn' => '不合法的子菜单按钮url长度'
        ],
        40029 => [
            'code' => 40029,
            'des_en' => 'invalid code',
            'des_cn' => '不合法或已过期的code'
        ],
        40030 => [
            'code' => 40030,
            'des_en' => 'invalid refresh_token',
            'des_cn' => '不合法的refresh_token'
        ],
        40036 => [
            'code' => 40036,
            'des_en' => 'invalid template_id size',
            'des_cn' => '不合法的template_id长度'
        ],
        40037 => [
            'code' => 40037,
            'des_en' => 'invalid template_id',
            'des_cn' => '不合法的template_id'
        ],
        40039 => [
            'code' => 40039,
            'des_en' => 'invalid url size',
            'des_cn' => '不合法的url长度'
        ],
        40048 => [
            'code' => 40048,
            'des_en' => 'invalid url domain',
            'des_cn' => '不合法的url域名'
        ],
        40054 => [
            'code' => 40054,
            'des_en' => 'invalid sub button url domain',
            'des_cn' => '不合法的子菜单按钮url域名'
        ],
        40055 => [
            'code' => 40055,
            'des_en' => 'invalid button url domain',
            'des_cn' => '不合法的菜单按钮url域名'
        ],
        40066 => [
            'code' => 40066,
            'des_en' => 'invalid url',
            'des_cn' => '不合法的url'
        ],
        41001 => [
            'code' => 41001,
            'des_en' => 'access_token missing',
            'des_cn' => '缺失access_token参数'
        ],
        41002 => [
            'code' => 41002,
            'des_en' => 'appid missing',
            'des_cn' => '缺失appid参数'
        ],
        41003 => [
            'code' => 41003,
            'des_en' => 'refresh_token missing',
            'des_cn' => '缺失refresh_token参数'
        ],
        41004 => [
            'code' => 41004,
            'des_en' => 'appsecret missing',
            'des_cn' => '缺失secret参数'
        ],
        41005 => [
            'code' => 41005,
            'des_en' => 'media data missing',
            'des_cn' => '缺失二进制媒体文件'
        ],
        41006 => [
            'code' => 41006,
            'des_en' => 'media_id missing',
            'des_cn' => '缺失media_id参数'
        ],
        41007 => [
            'code' => 41007,
            'des_en' => 'sub_menu data missing',
            'des_cn' => '缺失子菜单数据'
        ],
        41008 => [
            'code' => 41008,
            'des_en' => 'missing code',
            'des_cn' => '缺失code参数'
        ],
        41009 => [
            'code' => 41009,
            'des_en' => 'missing openid',
            'des_cn' => '缺失openid参数'
        ],
        41010 => [
            'code' => 41010,
            'des_en' => 'missing url',
            'des_cn' => '缺失url参数'
        ],
        41028 => [
            'code' => 41028,
            'des_en' => 'form_id error',
            'des_cn' => 'form_id不正确，或者过期'
        ],
        41029 => [
            'code' => 41029,
            'des_en' => 'form_id already used',
            'des_cn' => 'form_id已被使用'
        ],
        41030 => [
            'code' => 41030,
            'des_en' => 'page error',
            'des_cn' => 'page不正确'
        ],
        42001 => [
            'code' => 42001,
            'des_en' => 'access_token expired',
            'des_cn' => 'access_token超时'
        ],
        42002 => [
            'code' => 42002,
            'des_en' => 'refresh_token expired',
            'des_cn' => 'refresh_token超时'
        ],
        42003 => [
            'code' => 42003,
            'des_en' => 'code expired',
            'des_cn' => 'code超时'
        ],
        43001 => [
            'code' => 43001,
            'des_en' => 'require GET method',
            'des_cn' => '需要使用GET方法请求'
        ],
        43002 => [
            'code' => 43002,
            'des_en' => 'require POST method',
            'des_cn' => '需要使用POST方法请求'
        ],
        43003 => [
            'code' => 43003,
            'des_en' => 'require https',
            'des_cn' => '需要使用HTTPS'
        ],
        43004 => [
            'code' => 43004,
            'des_en' => 'require subscribe',
            'des_cn' => '需要订阅关系'
        ],
        44001 => [
            'code' => 44001,
            'des_en' => 'empty media data',
            'des_cn' => '空白的二进制数据'
        ],
        44002 => [
            'code' => 44002,
            'des_en' => 'empty post data',
            'des_cn' => '空白的POST数据'
        ],
        44003 => [
            'code' => 44003,
            'des_en' => 'empty news data',
            'des_cn' => '空白的news数据'
        ],
        44004 => [
            'code' => 44004,
            'des_en' => 'empty content',
            'des_cn' => '空白的内容'
        ],
        44005 => [
            'code' => 44005,
            'des_en' => 'empty list size',
            'des_cn' => '空白的列表'
        ],
        45001 => [
            'code' => 45001,
            'des_en' => 'media size out of limit',
            'des_cn' => '二进制文件超过限制'
        ],
        45002 => [
            'code' => 45002,
            'des_en' => 'content size out of limit',
            'des_cn' => 'content参数超过限制'
        ],
        45003 => [
            'code' => 45003,
            'des_en' => 'title size out of limit',
            'des_cn' => 'title参数超过限制'
        ],
        45004 => [
            'code' => 45004,
            'des_en' => 'description size out of limit',
            'des_cn' => 'description参数超过限制'
        ],
        45005 => [
            'code' => 45005,
            'des_en' => 'url size out of limit',
            'des_cn' => 'url参数长度超过限制'
        ],
        45006 => [
            'code' => 45006,
            'des_en' => 'picurl size out of limit',
            'des_cn' => 'picurl参数超过限制'
        ],
        45007 => [
            'code' => 45007,
            'des_en' => 'playtime out of limit',
            'des_cn' => '播放时间超过限制（语音为60s最大）'
        ],
        45008 => [
            'code' => 45008,
            'des_en' => 'article size out of limit',
            'des_cn' => 'article参数超过限制'
        ],
        45009 => [
            'code' => 45009,
            'des_en' => 'api freq out of limit',
            'des_cn' => '接口调动频率超过限制'
        ],
        45010 => [
            'code' => 45010,
            'des_en' => 'create menu limit',
            'des_cn' => '建立菜单被限制'
        ],
        45011 => [
            'code' => 45011,
            'des_en' => 'api limit',
            'des_cn' => '频率限制'
        ],
        45012 => [
            'code' => 45012,
            'des_en' => 'template size out of limit',
            'des_cn' => '模板大小超过限制'
        ],
        45016 => [
            'code' => 45016,
            'des_en' => 'can not modify sys group',
            'des_cn' => '不能修改默认组'
        ],
        45017 => [
            'code' => 45017,
            'des_en' => 'can not set group name too long sys group',
            'des_cn' => '修改组名过长'
        ],
        45018 => [
            'code' => 45018,
            'des_en' => 'too many group.now, no need to add new',
            'des_cn' => '组数量过多'
        ],
        50001 => [
            'code' => 50001,
            'des_en' => 'api unauthorized',
            'des_cn' => '接口未授权'
        ],
    ];

    public static function getCodeDes($code, $lang='cn')
    {
        if (isset(self::$codeList[$code])) {
            if ($lang == 'cn') {
                return self::$codeList[$code]['des_cn'];
            } else {
                return self::$codeList[$code]['des_en'];
            }
        }
        return 'unknown code';
    }
}
