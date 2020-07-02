<?php

namespace HyperfPlus\Retry;

use HyperfPlus\Constant\ErrorCode;
use HyperfPlus\Exception\RetryException;
use HyperfPlus\Log\Log;

/**
 * 错误重试组件（产生异常则进行重试机制）
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Retry
 */
class Retry
{
    /**
     * 错误重试
     *
     * @param $callable
     * @param int $retry 错误重试次数（0表示不执行错误重试机制）
     * @return mixed
     * @throws \Exception
     */
    public static function run($callable, $retry = 0)
    {
        if (!is_callable($callable)) throw new RetryException(ErrorCode::RETRY_CALLABLE_INVALID);

        if ($retry == 0) return call_user_func($callable);

        $tried = 0;

        do {
            try {
                // 执行回调
                return call_user_func($callable);
            } catch (\Exception $exception) {
                $tried++;
                Log::error("重试组件第{$tried}次执行失败！", ['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
            }
        } while (--$retry >= 0);

        Log::error('重试多次依然失败！', ['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
        throw new RetryException(ErrorCode::RETRY_ERROR);
    }
}