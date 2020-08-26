<?php

namespace HyperfPlus\Util;

use HyperfPlus\Redis\Redis;

/**
 * 常用工具类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Util
 */
class Util
{
    /**
     * 对象转数组
     *
     * @param $obj
     * @return array
     */
    public static function object2Array($obj)
    {
        return json_decode(json_encode($obj), true);
    }

    /**
     * 格式化查找接口结果
     *
     * @param $p
     * @param $size
     * @param $total
     * @param $list
     * @return array
     */
    public static function formatSearchRes($p, $size, $total, $list)
    {
        return [
            'p'         => $p,
            'size'      => $size,
            'total'     => $total,
            'next'      => $p * $size < $total ? 1 : 0,
            'list'      => $list
        ];
    }

    /**
     * 一个字符串中是否包含另外一个字符串
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function contain($haystack, $needle)
    {
        return strstr($haystack, $needle) !== false;
    }

    /**
     * 协程休眠
     *
     * @param int $seconds
     */
    public static function sleep($seconds = 1)
    {
        \Swoole\Coroutine::sleep($seconds);
    }

    /**
     * 获取当前格式化时间
     *
     * @return string
     */
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 获取 traceId
     *
     * @return string
     */
    public static function getTraceId()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * 获取 redis 分布式锁
     *
     * @param $key
     * @param int $timeout
     * @param string $poolName
     * @return bool
     */
    public static function getLock($key, $timeout = 1, $poolName = 'default')
    {
        return Redis::instance($poolName)->set($key, 1, ['nx', 'ex' => $timeout]);
    }

    /**
     * 删除 redis 分布式锁
     *
     * @param $key
     * @param string $poolName
     * @return int
     */
    public static function delLock($key, $poolName = 'default')
    {
        return Redis::instance($poolName)->del($key);
    }

    /**
     * 入队（基于 redis）
     *
     * @param $key
     * @param array $value
     * @param string $poolName
     * @return bool|int
     */
    public static function enqueueByRedis($key, $value = [], $poolName = 'default')
    {
        return Redis::instance($poolName)->lPush($key, json_encode($value));
    }

    /**
     * 出队（基于 redis）
     *
     * @param $key
     * @param string $poolName
     * @return mixed
     */
    public static function dequeueByRedis($key, $poolName = 'default')
    {
        $data = Redis::instance($poolName)->rPop($key);
        return json_decode($data, true);
    }

    /**
     * 将数组转换为对应的 redis key
     *
     * @param array $data
     * @return string
     */
    public static function generateRedisKeyByArrayData($data = [])
    {
        return md5(serialize($data));
    }
}