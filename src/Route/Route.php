<?php

namespace HyperfPlus\Route;

/**
 * 路由封装类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Route
 */
class Route
{
    /**
     * 路由装饰
     *
     * @param string $route
     * @return string
     */
    public static function decoration($route = '')
    {
        return sprintf('App\Module\%s@handle', $route);
    }
}