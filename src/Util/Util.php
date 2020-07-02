<?php

namespace HyperfPlus\Util;

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
     * 获取格式化时间戳（格式：1591927349.686）
     *
     * @return string
     */
    public static function getTimestampWithMilliSecond()
    {
        return time() . '.' . str_pad(intval(explode(' ', microtime())[0] * 1000), 3, '0', STR_PAD_LEFT);
    }
}