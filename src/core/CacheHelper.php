<?php
namespace jext\wechat\core;

use jext\jeen\JRedis;
use yii\caching\CacheInterface;
use yii\caching\FileCache;

class CacheHelper
{
    public static $instance;

    /**
     * 缓存 通常用来存储 accessToken 之类的信息
     * 注意避免多项目或分布式导致的动态数据缓存失效问题
     *
     * @param string $type
     * @return CacheInterface|mixed
     * @throws \Exception
     */
    public static function getInstance($type = 'redis')
    {
        if (empty(self::$instance[$type])) {
            switch ($type) {
                case 'redis' : {
                    self::$instance[$type] = JRedis::getInstance();
                } break;
                case 'file' : {
                    self::$instance[$type] = new FileCache([
                        'cachePath' => '/var/tmp/yiiCache',
                    ]);
                } break;
                default: {
                    self::$instance = \Yii::$app->getCache();
                } break;
            }
        }
        return self::$instance[$type];
    }

}
