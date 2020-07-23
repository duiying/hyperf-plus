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

    /**
     * 删除 key
     *
     * @param $key
     * @param string $poolName
     * @return int
     */
    public static function del($key, $poolName = 'default')
    {
        return self::getRedis($poolName)->del($key);
    }

    /******************** string begin ********************************************************************************/
    public static function set($key, $value, $timeout = null, $poolName = 'default')
    {
        return self::getRedis($poolName)->set($key, $value, $timeout);
    }

    public static function get($key, $poolName = 'default')
    {
        return self::getRedis($poolName)->get($key);
    }

    public static function setNx($key, $value, $timeout = 1, $poolName = 'default')
    {
        return self::getRedis($poolName)->set($key, $value, ['nx', 'ex' => $timeout]);
    }
    /******************** string end **********************************************************************************/


    /******************** list begin **********************************************************************************/
    public static function lPush($key, $value, $poolName = 'default')
    {
        return self::getRedis($poolName)->lPush($key, $value);
    }

    public static function lPop($key, $poolName = 'default')
    {
        return self::getRedis($poolName)->lPop($key);
    }

    public static function rPush($key, $value, $poolName = 'default')
    {
        return self::getRedis($poolName)->rPush($key, $value);
    }

    public static function rPop($key, $poolName = 'default')
    {
        return self::getRedis($poolName)->rPop($key);
    }

    public static function lLen($key, $poolName = 'default')
    {
        return self::getRedis($poolName)->lLen($key);
    }
    /******************** list end ************************************************************************************/

    /******************** zset begin **********************************************************************************/
    public static function zAdd($key, $score, $value, $options = [], $poolName = 'default')
    {
        return self::getRedis($poolName)->zAdd($key, $options, $score, $value);
    }

    public static function zRangeByScore($key, $start, $end, $options = array(), $poolName = 'default')
    {
        return self::getRedis($poolName)->zRangeByScore($key, $start, $end, $options);
    }
    /******************** zset end ************************************************************************************/
}