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
    public static function getRedis($poolName = 'default')
    {
        return ApplicationContext::getContainer()->get(RedisFactory::class)->get($poolName);
    }

    /******************** string begin ********************/
    public function set($key, $value, $timeout = null, $poolName = 'default')
    {
        return self::getRedis($poolName)->set($key, $value, $timeout);
    }

    public function setNx($key, $value, $timeout = 1, $poolName = 'default')
    {
        return self::getRedis($poolName)->set($key, $value, ['nx', 'ex' => $timeout]);
    }
    /******************** string end ********************/

    /******************** list begin ********************/
    public function lPush($key, $value, $poolName = 'default')
    {
        return self::getRedis($poolName)->lPush($key, $value);
    }

    public function lPop($key, $poolName = 'default')
    {
        return self::getRedis($poolName)->lPop($key);
    }

    public function rPush($key, $value, $poolName = 'default')
    {
        return self::getRedis($poolName)->rPush($key, $value);
    }

    public function rPop($key, $poolName = 'default')
    {
        return self::getRedis($poolName)->rPop($key);
    }
    /******************** list end ********************/
}