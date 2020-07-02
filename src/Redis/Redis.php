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

    /******************** string begin ********************/
    public function set($key, $value, $timeout = null, $poolName = 'default')
    {
        $container = ApplicationContext::getContainer();
        $redis = $container->get(RedisFactory::class)->get($poolName);
        return $redis->set($key, $value, $timeout);
    }

    public function setNx($key, $value, $timeout = 1, $poolName = 'default')
    {
        $container = ApplicationContext::getContainer();
        $redis = $container->get(RedisFactory::class)->get($poolName);
        return $redis->set($key, $value, ['nx', 'ex' => $timeout]);
    }
    /******************** string end ********************/

    /******************** list begin ********************/
    public function lPush($key, $value, $poolName = 'default')
    {
        $container = ApplicationContext::getContainer();
        $redis = $container->get(RedisFactory::class)->get($poolName);
        return $redis->lPush($key, $value);
    }

    public function lPop($key, $poolName = 'default')
    {
        $container = ApplicationContext::getContainer();
        $redis = $container->get(RedisFactory::class)->get($poolName);
        return $redis->lPop($key);
    }

    public function rPush($key, $value, $poolName = 'default')
    {
        $container = ApplicationContext::getContainer();
        $redis = $container->get(RedisFactory::class)->get($poolName);
        return $redis->rPush($key, $value);
    }

    public function rPop($key, $poolName = 'default')
    {
        $container = ApplicationContext::getContainer();
        $redis = $container->get(RedisFactory::class)->get($poolName);
        return $redis->rPop($key);
    }
    /******************** list end ********************/
}