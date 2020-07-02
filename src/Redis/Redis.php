<?php

namespace HyperfPlus\Redis;

use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;

/**
 * redis封装类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Redis
 */
class Redis
{
    public static function set($key, $value, $timeout = null, $poolName = 'default')
    {
        $container = ApplicationContext::getContainer();
        $redis = $container->get(RedisFactory::class)->get($poolName);
        return $redis->set($key, $value, $timeout);
    }
}