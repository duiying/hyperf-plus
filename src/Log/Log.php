<?php

namespace HyperfPlus\Log;

use Hyperf\Utils\ApplicationContext;

/**
 * 日志封装类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Log
 */
class Log
{
    public static function info($message, array $context = [], $name = 'app')
    {
        $logger = ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name, 'info');
        $logger->info($message, $context);
    }

    public static function error($message, array $context = [], $name = 'app')
    {
        $logger = ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name, 'error');
        $logger->error($message, $context);
    }

    public static function debug($message, array $context = [], $name = 'app')
    {
        $logger = ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name, 'debug');
        $logger->debug($message, $context);
    }

    public static function warning($message, array $context = [], $name = 'app')
    {
        $logger = ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name, 'warning');
        $logger->warning($message, $context);
    }

    public static function notice($message, array $context = [], $name = 'app')
    {
        $logger = ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name, 'notice');
        $logger->notice($message, $context);
    }
}