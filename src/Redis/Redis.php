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
    /**
     * 获取 redis 实例
     *
     * @param string $poolName
     * @return \Hyperf\Redis\RedisProxy|\Redis|null
     */
    public static function instance($poolName = 'default')
    {
        return ApplicationContext::getContainer()->get(RedisFactory::class)->get($poolName);
    }
}